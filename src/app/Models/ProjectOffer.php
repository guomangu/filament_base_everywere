<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class ProjectOffer extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'title',
        'description',
        'images',
        'type',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'project_id' => 'integer',
            'images' => 'array',
            'rating' => 'integer', // If added to offer directly or virtual
        ];
    }

    // Relations
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'project_offer_skill', 'project_offer_id', 'skill_id');
    }

    public function reviews(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ProjectReview::class, 'project_offer_id');
    }

    public function informations(): MorphMany
    {
        return $this->morphMany(Information::class, 'informable');
    }

    // Attributes
    public function getAverageRatingAttribute()
    {
        return $this->reviews()->whereNull('parent_id')->avg('rating') ?: 0;
    }

    public function getReviewsCountAttribute()
    {
        return $this->reviews()->whereNull('parent_id')->count();
    }

    // Scopes
    public function scopeOffers($query)
    {
        return $query->where('type', 'offer');
    }

    public function scopeDemands($query)
    {
        return $query->where('type', 'demand');
    }

    // Helper methods
    public function isOffer(): bool
    {
        return $this->type === 'offer';
    }

    public function isDemand(): bool
    {
        return $this->type === 'demand';
    }
}
