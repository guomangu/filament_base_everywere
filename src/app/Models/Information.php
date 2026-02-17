<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Information extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'label',
        'images',
        'informable_id',
        'informable_type',
        'author_id',
    ];

    protected $casts = [
        'images' => 'array',
    ];

    /**
     * Get the parent informable model.
     */
    public function informable()
    {
        return $this->morphTo();
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
