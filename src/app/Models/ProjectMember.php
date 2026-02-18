<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ProjectMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'memberable_type',
        'memberable_id',
        'role',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'project_id' => 'integer',
        ];
    }

    // Relations
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function memberable(): MorphTo
    {
        return $this->morphTo();
    }

    // Helper methods
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function activate(): void
    {
        $this->status = 'active';
        $this->save();
    }

    public function deactivate(): void
    {
        $this->status = 'inactive';
        $this->save();
    }

    public function promoteToAdmin(): void
    {
        $this->role = 'admin';
        $this->save();
    }

    public function demoteToMember(): void
    {
        $this->role = 'member';
        $this->save();
    }
}
