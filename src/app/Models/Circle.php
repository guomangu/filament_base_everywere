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
}
