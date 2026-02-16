<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CircleMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'circle_id',
        'user_id',
        'role',
        'status',
        'vouched_by_id',
        'joined_at',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'circle_id' => 'integer',
            'user_id' => 'integer',
            'vouched_by_id' => 'integer',
            'joined_at' => 'timestamp',
        ];
    }

    public function circle(): BelongsTo
    {
        return $this->belongsTo(Circle::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function voucher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vouched_by_id');
    }
}
