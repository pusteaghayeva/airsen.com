<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'avatar',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isModerator(): bool
    {
        return in_array($this->role, ['super_admin', 'moderator']);
    }

    public function isAnalyst(): bool
    {
        return in_array($this->role, ['super_admin', 'analyst']);
    }

    public function getRoleTitleAttribute(): string
    {
        return match ($this->role) {
            'super_admin' => 'Super Admin',
            'moderator' => 'Moderator',
            'analyst' => 'Analitik',
            default => 'İstifadəçi',
        };
    }

    public function getRoleBadgeClassAttribute(): string
    {
        return match ($this->role) {
            'super_admin' => 'pill-purple',
            'moderator' => 'pill-blue',
            'analyst' => 'pill-green',
            default => 'pill-dim',
        };
    }

    public function getAvatarInitialAttribute(): string
    {
        return strtoupper(substr($this->name, 0, 1));
    }
}
