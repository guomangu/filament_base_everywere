<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'user_id',
        'type',
        'comment',
        'parent_id',
        'rating',
        'project_offer_id',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'project_id' => 'integer',
            'user_id' => 'integer',
            'parent_id' => 'integer',
        ];
    }

    // Relations
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ProjectReview::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(ProjectReview::class, 'parent_id');
    }

    public function offer(): BelongsTo
    {
        return $this->belongsTo(ProjectOffer::class, 'project_offer_id');
    }

    // Helper methods
    public function isValidation(): bool
    {
        return $this->type === 'validate';
    }

    public function isRejection(): bool
    {
        return $this->type === 'reject';
    }

    public function isReply(): bool
    {
        return $this->parent_id !== null;
    }

    public function isTopLevel(): bool
    {
        return $this->parent_id === null;
    }
}
