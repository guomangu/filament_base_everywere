<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Circle extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'type',
        'address',
        'coordinates',
        'owner_id',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'coordinates' => 'array',
            'owner_id' => 'integer',
        ];
    }

    public function getTrustScoreAttribute(): int
    {
        return $this->getAverageTrustScore();
    }

    public function activeProject(): ?Project
    {
        return Project::whereHas('members', function($q) {
            $q->where('memberable_type', Circle::class)
              ->where('memberable_id', $this->id)
              ->where('status', 'active');
        })->where('is_open', true)->first();
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(CircleMember::class);
    }

    public function activeMembers(): HasMany
    {
        return $this->hasMany(CircleMember::class)->where('status', 'active');
    }

    public function addMember(User $user, string $role = 'member', string $status = 'active'): CircleMember
    {
        $member = $this->members()->where('user_id', $user->id)->first();

        if ($member) {
            $member->update(['status' => $status, 'role' => $role]);
            return $member;
        }

        return $this->members()->create([
            'user_id' => $user->id,
            'role' => $role,
            'status' => $status,
            'joined_at' => now(),
        ]);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function informations()
    {
        return $this->morphMany(Information::class, 'informable');
    }

    public function achievements(): HasMany
    {
        return $this->hasMany(Achievement::class);
    }

    /**
     * Calculate the average trust score of the circle based on owner and active members
     */
    public function getAverageTrustScore(): int
    {
        // Get owner score
        $ownerScore = $this->owner->trust_score ?? 0;
        
        // Get active members scores
        $memberScores = $this->activeMembers()
            ->with('user')
            ->get()
            ->pluck('user.trust_score')
            ->filter()
            ->toArray();
        
        // Combine owner + members
        $allScores = array_merge([$ownerScore], $memberScores);
        
        // Calculate average
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
        // Get owner achievements
        $ownerAchievements = $this->owner->achievements()
            ->where('title', '!=', '__SKELETON__')
            ->with(['skill', 'validations.user'])
            ->get();
        
        // Get active members achievements
        $memberAchievements = Achievement::whereHas('user.circleMembers', function($q) {
            $q->where('circle_id', $this->id)
              ->where('status', 'active');
        })
        ->where('title', '!=', '__SKELETON__')
        ->with(['skill', 'validations.user'])
        ->get();
        
        // Combine and return
        return $ownerAchievements->merge($memberAchievements)->unique('id');
    }

    /**
     * Get all validations received by circle members
     */
    public function getAllMemberValidations()
    {
        $memberUserIds = $this->activeMembers()
            ->pluck('user_id')
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
        // Get circle's own information
        $circleInfo = $this->informations;
        
        // Get owner information
        $ownerInfo = $this->owner->informations;
        
        // Get active members information
        $memberUserIds = $this->activeMembers()->pluck('user_id');
        $memberInfo = \App\Models\Information::where('informable_type', 'App\\Models\\User')
            ->whereIn('informable_id', $memberUserIds)
            ->get();
        
        // Combine and return unique
        return $circleInfo->merge($ownerInfo)->merge($memberInfo)->unique('id');
    }

    /**
     * Count validated achievements (with at least one positive validation) from all members
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
