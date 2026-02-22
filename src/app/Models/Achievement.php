<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Achievement extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'skill_id',
        'circle_id',
        'title',
        'description',
        'media_url',
        'metadata',
        'is_verified',
        'proche_id',
        'realized_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'user_id' => 'integer',
            'skill_id' => 'integer',
            'circle_id' => 'integer',
            'proche_id' => 'integer',
            'metadata' => 'array',
            'is_verified' => 'boolean',
            'realized_at' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function skill(): BelongsTo
    {
        return $this->belongsTo(Skill::class);
    }

    public function circle(): BelongsTo
    {
        return $this->belongsTo(Circle::class);
    }

    public function proche(): BelongsTo
    {
        return $this->belongsTo(Proche::class);
    }

    public function informations()
    {
        return $this->morphMany(Information::class, 'informable')->latest();
    }

    public function validations()
    {
        return $this->hasMany(AchievementValidation::class);
    }
}
