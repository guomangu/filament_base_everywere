<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    public function getAvatarAttribute()
    {
        return $this->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=random&color=fff';
    }

    public function vouchesReceived(): HasMany
    {
        return $this->hasMany(Vouch::class, 'vouchee_id');
    }

    public function vouchesGiven(): HasMany
    {
        return $this->hasMany(Vouch::class, 'voucher_id');
    }

    public function recalculateTrustScore()
    {
        // Base score is 10 (neutral)
        $score = 10;
        
        // Impact from vouches: sum of (guarantor_trust_score / 10)
        // This means a guarantor with 90% trust adds 9 points.
        $vouchImpact = $this->vouchesReceived()->with('voucher')->get()->sum(function($vouch) {
            return round($vouch->voucher->trust_score / 10);
        });

        $score += $vouchImpact;

        // Cap at 100
        $this->update(['trust_score' => min(100, $score)]);
    }

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar_url',
        'bio',
        'location',
        'coordinates',
        'trust_score',
        'is_admin',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'coordinates' => 'array',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_admin;
    }

    public function ownedCircles(): HasMany
    {
        return $this->hasMany(Circle::class, 'owner_id');
    }

    public function achievements(): HasMany
    {
        return $this->hasMany(Achievement::class);
    }

    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function joinedCircles()
    {
        return $this->belongsToMany(Circle::class, 'circle_members')
            ->withPivot(['role', 'status', 'vouched_by_id', 'joined_at'])
            ->withTimestamps();
    }

    public function activeJoinedCircles()
    {
        return $this->belongsToMany(Circle::class, 'circle_members')
            ->wherePivot('status', 'active')
            ->withPivot(['role', 'status', 'vouched_by_id', 'joined_at'])
            ->withTimestamps();
    }

    public function circleMembers(): HasMany
    {
        return $this->hasMany(CircleMember::class);
    }

    public function informations()
    {
        return $this->morphMany(Information::class, 'informable');
    }
}
