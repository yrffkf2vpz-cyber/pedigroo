<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
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
        ];
    }

    public function isSuperAdmin(): bool
    {
        return (bool) $this->super_admin;
    }

    public function isBreedAdmin(): bool
    {
        return !is_null($this->breed_id);
    }

    public function getLocale(): string
    {
        return $this->locale ?? 'en';
    }

    // -----------------------------
    // ROLE & PERMISSION RÉTEG
    // -----------------------------

    public function roles()
    {
        return $this->belongsToMany(\App\Modules\User\Models\Role::class, 'user_role');
    }

    public function directPermissions()
    {
        return collect();
    }

    public function allPermissions()
    {
        return $this->roles
            ->loadMissing('permissions')
            ->flatMap->permissions
            ->merge($this->directPermissions())
            ->unique('id')
            ->values();
    }

    public function hasRole(string $role): bool
    {
        return $this->roles->contains('name', $role);
    }

    public function hasAnyRole(array $roles): bool
    {
        return $this->roles->pluck('name')->intersect($roles)->isNotEmpty();
    }

    public function hasPermission(string $permission): bool
    {
        return $this->allPermissions()->contains('name', $permission);
    }
}

