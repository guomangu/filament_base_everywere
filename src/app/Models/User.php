<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use App\Models\Circle;
use App\Models\CircleMember;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    public function getAvatarAttribute()
    {
        return $this->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=random&color=fff';
    }

    public function vouchesReceived(): HasMany
    {
        return $this->hasMany(Vouch::class, 'vouchee_id');
    }

    public function vouchesGiven(): HasMany
    {
        return $this->hasMany(Vouch::class, 'voucher_id');
    }

    public function validationsReceived(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(AchievementValidation::class, Achievement::class);
    }

    public function recalculateTrustScore()
    {
        // Base score is 10 (neutral)
        $score = 10;
        
        // Impact from vouches: sum of (guarantor_trust_score / 10)
        $vouchImpact = $this->vouchesReceived()->with('voucher')->get()->sum(function($vouch) {
            return round($vouch->voucher->trust_score / 10);
        });

        // Impact from Achievement Validations:
        // Each validation adds points, each rejection removes points
        $validationImpact = $this->validationsReceived()->get()->sum(function($v) {
            return $v->type === 'validate' ? 2 : -5;
        });

        $score += $vouchImpact + $validationImpact;

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

    public function getTrustPathTo(User $other): array
    {
        if ($this->id === $other->id) {
            return [['type' => 'user', 'id' => $this->id, 'name' => 'Vous', 'avatar' => $this->avatar]];
        }

        // 1st Degree: Common Circles
        $myCircleIds = $this->activeJoinedCircles->pluck('id');
        $otherCircleIds = $other->activeJoinedCircles->pluck('id');
        $commonCircles = $myCircleIds->intersect($otherCircleIds);

        if ($commonCircles->isNotEmpty()) {
            $circle = Circle::find($commonCircles->first());
            return [
                ['type' => 'user', 'id' => $this->id, 'name' => 'Vous', 'avatar' => $this->avatar],
                ['type' => 'circle', 'id' => $circle->id, 'name' => $circle->name],
                ['type' => 'user', 'id' => $other->id, 'name' => $other->name, 'avatar' => $other->avatar]
            ];
        }

        // 2nd Degree: Intermediaries
        // Get all members of my circles
        $intermediaries = CircleMember::whereIn('circle_id', $myCircleIds)
            ->where('status', 'active')
            ->where('user_id', '!=', $this->id)
            ->with(['user', 'circle'])
            ->get();

        foreach ($intermediaries as $member) {
            $intermediary = $member->user;
            $sharedWithMe = $member->circle;
            
            // Check if this intermediary shares a circle with $other
            $interCircleIds = $intermediary->activeJoinedCircles->pluck('id');
            $commonWithOther = $interCircleIds->intersect($otherCircleIds);
            
            if ($commonWithOther->isNotEmpty()) {
                $sharedWithOther = Circle::find($commonWithOther->first());
                return [
                    ['type' => 'user', 'id' => $this->id, 'name' => 'Vous', 'avatar' => $this->avatar],
                    ['type' => 'circle', 'id' => $sharedWithMe->id, 'name' => $sharedWithMe->name],
                    ['type' => 'user', 'id' => $intermediary->id, 'name' => $intermediary->name, 'avatar' => $intermediary->avatar],
                    ['type' => 'circle', 'id' => $sharedWithOther->id, 'name' => $sharedWithOther->name],
                    ['type' => 'user', 'id' => $other->id, 'name' => $other->name, 'avatar' => $other->avatar]
                ];
            }
        }

        return [];
    }
}
