<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'circle_id',
        'sender_id',
        'title',
        'content',
        'type',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'circle_id' => 'integer',
            'sender_id' => 'integer',
            'metadata' => 'array',
        ];
    }

    public function circle(): BelongsTo
    {
        return $this->belongsTo(Circle::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
