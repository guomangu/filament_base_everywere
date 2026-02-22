<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Skill extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'category',
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
        ];
    }

    public function achievements(): HasMany
    {
        return $this->hasMany(Achievement::class);
    }

    public function informations()
    {
        return $this->morphMany(Information::class, 'informable');
    }

    public function projectOffers(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(ProjectOffer::class, 'project_offer_skill');
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }
}
