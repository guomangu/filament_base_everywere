<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use App\Models\Circle;
use App\Models\CircleMember;
use App\Models\Proche;
use App\Models\Achievement;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    public function getAvatarAttribute()
    {
        return $this->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=random&color=fff';
    }


    public function validationsReceived(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(AchievementValidation::class, Achievement::class);
    }

    public function recalculateTrustScore()
    {
        // Base score: 20
        $score = 20;
        
        // Impact from Achievement Validations:
        // +12 per validation, -25 per rejection
        $validations = $this->validationsReceived()->get();
        $valCount = $validations->where('type', 'validate')->count();
        $rejCount = $validations->where('type', 'reject')->count();
        
        $validationImpact = ($valCount * 12) - ($rejCount * 25);

        // Impact from Circle participation:
        // +8 per active circle membership
        $circleImpact = $this->activeJoinedCircles()->count() * 8;

        $score += $validationImpact + $circleImpact;

        // Cap between 0 and 100
        $this->update(['trust_score' => max(0, min(100, $score))]);
    }

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar_url',
        'bio',
        'location',
        'coordinates',
        'trust_score',
        'is_admin',
        'parent_id',
        'is_managed',
        'transfer_token',
        'transfer_code',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'coordinates' => 'array',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'parent_id' => 'integer',
            'is_managed' => 'boolean',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_admin;
    }

    public function ownedCircles(): HasMany
    {
        return $this->hasMany(Circle::class, 'owner_id');
    }

    public function ownedProjects(): HasMany
    {
        return $this->hasMany(Project::class, 'owner_id');
    }

    public function projectMemberships(): MorphMany
    {
        return $this->morphMany(ProjectMember::class, 'memberable');
    }

    public function activeProjectMemberships(): MorphMany
    {
        return $this->morphMany(ProjectMember::class, 'memberable')->where('status', 'active');
    }

    public function projectReviews(): HasMany
    {
        return $this->hasMany(ProjectReview::class);
    }

    public function achievements(): HasMany
    {
        return $this->hasMany(Achievement::class);
    }

    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function joinedCircles()
    {
        return $this->belongsToMany(Circle::class, 'circle_members')
            ->withPivot(['role', 'status', 'vouched_by_id', 'joined_at'])
            ->withTimestamps();
    }

    public function activeJoinedCircles()
    {
        return $this->belongsToMany(Circle::class, 'circle_members')
            ->wherePivot('status', 'active')
            ->withPivot(['role', 'status', 'vouched_by_id', 'joined_at'])
            ->withTimestamps();
    }

    public function activeProject(): ?Project
    {
        // First check owned projects that are open
        $owned = $this->ownedProjects()->where('is_open', true)->first();
        if ($owned) return $owned;

        // Then check memberships in open projects
        return Project::whereHas('members', function($q) {
            $q->where('memberable_type', User::class)
              ->where('memberable_id', $this->id)
              ->where('status', 'active');
        })->where('is_open', true)->first();
    }

    public function circleMembers(): HasMany
    {
        return $this->hasMany(CircleMember::class);
    }

    public function informations()
    {
        return $this->morphMany(Information::class, 'informable');
    }

    // Proches relationships
    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function proches(): HasMany
    {
        return $this->hasMany(Proche::class, 'parent_id');
    }

    public function getTrustPathTo($other): array
    {
        $path = [['type' => 'user', 'id' => $this->id, 'name' => 'Vous', 'avatar' => $this->avatar]];

        if ($other instanceof User && $this->id === $other->id) {
            return $path;
        }

        // Helper to find path to a target User
        $findUserPath = function(User $target) use ($path) {
            if ($this->id === $target->id) return $path;

            // 1st Degree: Common Circles
            $myCircleIds = $this->activeJoinedCircles->pluck('id');
            $otherCircleIds = $target->activeJoinedCircles->pluck('id');
            $commonCircles = $myCircleIds->intersect($otherCircleIds);

            if ($commonCircles->isNotEmpty()) {
                $circle = Circle::find($commonCircles->first());
                return array_merge($path, [
                    ['type' => 'circle', 'id' => $circle->id, 'name' => $circle->name],
                    ['type' => 'user', 'id' => $target->id, 'name' => $target->name, 'avatar' => $target->avatar]
                ]);
            }

            // 1.5 Degree: Common Region
            $myRegions = $this->activeJoinedCircles->pluck('region')->filter()->unique();
            $targetRegions = $target->activeJoinedCircles->pluck('region')->filter()->unique();
            $commonRegions = $myRegions->intersect($targetRegions);

            if ($commonRegions->isNotEmpty()) {
                $regionName = $commonRegions->first();
                $myCircle = $this->activeJoinedCircles->where('region', $regionName)->first();
                $targetCircle = $target->activeJoinedCircles->where('region', $regionName)->first();

                return array_merge($path, [
                    ['type' => 'circle', 'id' => $myCircle->id, 'name' => $myCircle->name],
                    ['type' => 'region', 'name' => $regionName],
                    ['type' => 'circle', 'id' => $targetCircle->id, 'name' => $targetCircle->name],
                    ['type' => 'user', 'id' => $target->id, 'name' => $target->name, 'avatar' => $target->avatar]
                ]);
            }

            // 2nd Degree: Intermediaries
            $intermediaries = CircleMember::whereIn('circle_id', $myCircleIds)
                ->where('status', 'active')
                ->where('user_id', '!=', $this->id)
                ->with(['user', 'circle'])
                ->get();

            foreach ($intermediaries as $member) {
                $intermediary = $member->user;
                $sharedWithMe = $member->circle;
                $interCircleIds = $intermediary->activeJoinedCircles->pluck('id');
                $commonWithOther = $interCircleIds->intersect($otherCircleIds);
                
                if ($commonWithOther->isNotEmpty()) {
                    $sharedWithOther = Circle::find($commonWithOther->first());
                    return array_merge($path, [
                        ['type' => 'circle', 'id' => $sharedWithMe->id, 'name' => $sharedWithMe->name],
                        ['type' => 'user', 'id' => $intermediary->id, 'name' => $intermediary->name, 'avatar' => $intermediary->avatar],
                        ['type' => 'circle', 'id' => $sharedWithOther->id, 'name' => $sharedWithOther->name],
                        ['type' => 'user', 'id' => $target->id, 'name' => $target->name, 'avatar' => $target->avatar]
                    ]);
                }
            }
            return [];
        };

        if ($other instanceof User) {
            return $findUserPath($other);
        }

        if ($other instanceof Circle) {
            // 1. Direct: I am a member
            if ($this->activeJoinedCircles->contains('id', $other->id)) {
                return array_merge($path, [
                    ['type' => 'circle', 'id' => $other->id, 'name' => $other->name]
                ]);
            }

            // 2. Intermediary: Path through a friend who is a member of this Circle
            $myCircleIds = $this->activeJoinedCircles->pluck('id');
            $interMember = CircleMember::whereIn('circle_id', $myCircleIds)
                ->where('status', 'active')
                ->where('user_id', '!=', $this->id)
                ->whereHas('user.circleMembers', function($q) use ($other) {
                    $q->where('circle_id', $other->id)->where('status', 'active');
                })
                ->with(['user', 'circle'])
                ->first();

            if ($interMember) {
                 return array_merge($path, [
                    ['type' => 'circle', 'id' => $interMember->circle_id, 'name' => $interMember->circle->name],
                    ['type' => 'user', 'id' => $interMember->user_id, 'name' => $interMember->user->name, 'avatar' => $interMember->user->avatar],
                    ['type' => 'circle', 'id' => $other->id, 'name' => $other->name]
                ]);
            }

            // 3. Indirect: Path to owner, then Circle
            $ownerPath = $this->getTrustPathTo($other->owner);
            if (empty($ownerPath)) return [];
            
            // Check if last element is already this circle
            if (end($ownerPath)['type'] === 'circle' && end($ownerPath)['id'] === $other->id) {
                return $ownerPath;
            }

            return array_merge($ownerPath, [
                ['type' => 'circle', 'id' => $other->id, 'name' => $other->name]
            ]);
        }

        if ($other instanceof Proche) {
            $userPath = $findUserPath($other->parent);
            if (empty($userPath)) return [];
            return array_merge($userPath, [
                ['type' => 'proche', 'id' => $other->id, 'name' => $other->name, 'user_id' => $other->parent_id]
            ]);
        }

        if ($other instanceof Achievement) {
            $basePath = [];
            $ownerId = null;

            if ($other->proche_id) {
                $proche = $other->proche ?? Proche::find($other->proche_id);
                if ($proche) {
                    $basePath = $this->getTrustPathTo($proche);
                    $ownerId = $proche->parent_id;
                }
            } elseif ($other->user_id) {
                $user = $other->user ?? User::find($other->user_id);
                if ($user) {
                    $basePath = $this->getTrustPathTo($user);
                    $ownerId = $user->id;
                }
            }

            if (empty($basePath)) return [];

            return array_merge($basePath, [
                ['type' => 'skill', 'id' => $other->skill_id, 'name' => $other->skill->name ?? 'Compétence', 'user_id' => $ownerId],
                ['type' => 'achievement', 'id' => $other->id, 'name' => $other->title, 'user_id' => $ownerId]
            ]);
        }

        return [];
    }
}
