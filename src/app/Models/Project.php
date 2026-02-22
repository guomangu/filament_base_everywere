<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'owner_id',
        'is_open',
        'address',
        'coordinates',
        'skill_id',
        'status',
        'realized_at',
        'locked_at',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'owner_id' => 'integer',
            'is_open' => 'boolean',
            'coordinates' => 'array',
            'skill_id' => 'integer',
            'realized_at' => 'datetime',
            'locked_at' => 'datetime',
        ];
    }

    public function skill(): BelongsTo
    {
        return $this->belongsTo(Skill::class);
    }

    // Relations
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(ProjectMember::class);
    }

    public function activeMembers(): HasMany
    {
        return $this->hasMany(ProjectMember::class)->where('status', 'active');
    }

    public function offers(): HasMany
    {
        return $this->hasMany(ProjectOffer::class)->where('type', 'offer');
    }

    public function demands(): HasMany
    {
        return $this->hasMany(ProjectOffer::class)->where('type', 'demand');
    }

    public function allOffers(): HasMany
    {
        return $this->hasMany(ProjectOffer::class);
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'project_skill');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ProjectReview::class)->whereNull('parent_id');
    }

    public function allReviews(): HasMany
    {
        return $this->hasMany(ProjectReview::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->latest();
    }

    public function informations(): MorphMany
    {
        return $this->morphMany(Information::class, 'informable')->latest();
    }

    // Scopes
    public function scopeOpen($query)
    {
        return $query->where('is_open', true);
    }

    public function scopeClosed($query)
    {
        return $query->where('is_open', false);
    }

    // Business Logic
    public function toggleStatus(): void
    {
        $this->is_open = !$this->is_open;
        $this->save();
    }

    public function addMember($memberable, string $role = 'member', string $status = 'active'): ProjectMember
    {
        // First check if a record already exists (e.g. pending/invited)
        $member = $this->members()
            ->where('memberable_type', get_class($memberable))
            ->where('memberable_id', $memberable->id)
            ->first();

        if ($member) {
            $member->update(['status' => $status, 'role' => $role]);
            return $member;
        }

        return $this->members()->create([
            'memberable_type' => get_class($memberable),
            'memberable_id' => $memberable->id,
            'role' => $role,
            'status' => $status,
        ]);
    }

    public function removeMember($memberable): bool
    {
        return $this->members()
            ->where('memberable_type', get_class($memberable))
            ->where('memberable_id', $memberable->id)
            ->delete() > 0;
    }

    public function isMember($memberable): bool
    {
        return $this->members()
            ->where('memberable_type', get_class($memberable))
            ->where('memberable_id', $memberable->id)
            ->where('status', 'active')
            ->exists();
    }

    public function isPending($memberable): bool
    {
        return $this->members()
            ->where('memberable_type', get_class($memberable))
            ->where('memberable_id', $memberable->id)
            ->where('status', 'pending')
            ->exists();
    }

    public function isInvited($memberable): bool
    {
        return $this->members()
            ->where('memberable_type', get_class($memberable))
            ->where('memberable_id', $memberable->id)
            ->where('status', 'invited')
            ->exists();
    }

    public function isOwner($user): bool
    {
        return $user && $this->owner_id === $user->id;
    }

    public function canManage($user): bool
    {
        if (!$user) {
            return false;
        }

        if ($this->isOwner($user)) {
            return true;
        }

        return $this->members()
            ->where('memberable_type', User::class)
            ->where('memberable_id', $user->id)
            ->where('role', 'admin')
            ->where('status', 'active')
            ->exists();
    }

    public function getPositiveReviewsCount(): int
    {
        return $this->reviews()->where('type', 'validate')->count();
    }

    public function getNegativeReviewsCount(): int
    {
        return $this->reviews()->where('type', 'reject')->count();
    }

    public function allSkills()
    {
        return $this->skills;
    }

    /**
     * Calculate the average trust score of the project based on owner and active members
     */
    public function getAverageTrustScore(): int
    {
        $ownerScore = $this->owner->trust_score ?? 0;
        
        $memberScores = $this->activeMembers()
            ->with('memberable')
            ->get()
            ->pluck('memberable.trust_score')
            ->filter()
            ->toArray();
        
        $allScores = array_merge([$ownerScore], $memberScores);
        
        if (empty($allScores)) {
            return 0;
        }
        
        return (int) round(array_sum($allScores) / count($allScores));
    }

    /**
     * Get all achievements from owner and active members
     */
    public function getAllMemberAchievements()
    {
        $ownerAchievements = $this->owner->achievements()
            ->where('title', '!=', '__SKELETON__')
            ->with(['skill', 'validations.user'])
            ->get();
            
        $memberAchievements = Achievement::whereHas('user.projectMemberships', function($q) {
            $q->where('project_id', $this->id)
              ->where('status', 'active');
        })
        ->where('title', '!=', '__SKELETON__')
        ->with(['skill', 'validations.user'])
        ->get();
        
        return $ownerAchievements->merge($memberAchievements)->unique('id');
    }

    /**
     * Get all validations received by project members
     */
    public function getAllMemberValidations()
    {
        $memberUserIds = $this->activeMembers()
            ->pluck('memberable_id')
            ->push($this->owner_id)
            ->unique();
        
        return \App\Models\AchievementValidation::whereHas('achievement', function($q) use ($memberUserIds) {
            $q->whereIn('user_id', $memberUserIds);
        })->get();
    }

    /**
     * Get all information from owner and active members
     */
    public function getAllMemberInformation()
    {
        $projectInfo = $this->informations;
        $ownerInfo = $this->owner->informations;
        
        $memberUserIds = $this->activeMembers()
            ->where('memberable_type', User::class)
            ->pluck('memberable_id');
            
        $memberInfo = \App\Models\Information::where('informable_type', User::class)
            ->whereIn('informable_id', $memberUserIds)
            ->get();
        
        return $projectInfo->merge($ownerInfo)->merge($memberInfo)->unique('id');
    }

    /**
     * Count validated achievements across all members
     */
    public function getValidatedAchievementsCount(): int
    {
        return $this->getAllMemberAchievements()
            ->filter(function($achievement) {
                return $achievement->validations->where('type', 'validate')->count() > 0;
            })
            ->count();
    }

    /**
     * Get a shorter version of the address (usually just the city)
     */
    public function getShortAddressAttribute(): ?string
    {
        $neighborhood = $this->neighborhood;
        $city = $this->city;

        if ($neighborhood && $city) {
            return $neighborhood . ', ' . $city;
        }
        
        return $city ?: $this->address;
    }

    /**
     * Get the neighborhood part of the address
     */
    public function getNeighborhoodAttribute(): ?string
    {
        if (!$this->address) return null;
        $parts = array_map('trim', explode(',', $this->address));
        return (count($parts) >= 4) ? $parts[1] : null;
    }

    /**
     * Get the city part of the address
     */
    public function getCityAttribute(): ?string
    {
        if (!$this->address) return null;
        $parts = array_map('trim', explode(',', $this->address));
        $count = count($parts);

        if ($count >= 4) return $parts[2];
        if ($count === 3) return $parts[1];
        if ($count === 2) return $parts[0];
        
    }
}
