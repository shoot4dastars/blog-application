<?php

namespace App\Models;

use App\Enums\RoleName;
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
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class)->withPivot('type');
    }

    public function hasRole(RoleName|string $role): bool
    {
        if (is_string($role)) {
            $role = RoleName::tryFrom($role);
        }

        return $this->roles->contains('name', $role);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(RoleName::ADMIN);
    }

    public function hasPermission($permissionRoute)
    {
        if ($this->permissions()->where('route_name', $permissionRoute)->where('type', 'include')->exists()) {
            return true;
        }

        foreach ($this->roles as $role) {
            if ($role->hasPermission($permissionRoute)) {
                return true;
            }
        }

        return false;
    }
}
