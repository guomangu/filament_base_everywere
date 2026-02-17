<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Proche extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'name',
        'transfer_token',
        'transfer_code',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function achievements(): HasMany
    {
        return $this->hasMany(Achievement::class);
    }
}
