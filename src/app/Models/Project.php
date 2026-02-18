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
    ];

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'owner_id' => 'integer',
            'is_open' => 'boolean',
            'coordinates' => 'array',
        ];
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
        return $this->hasMany(Message::class);
    }

    public function informations(): MorphMany
    {
        return $this->morphMany(Information::class, 'informable');
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
        return $this->owner_id === $user->id;
    }

    public function canManage($user): bool
    {
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
