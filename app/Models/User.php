<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'pemda_scope',
        'kl_scope',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'role' => UserRole::class,
        'pemda_scope' => 'array',
        'kl_scope' => 'array',
    ];

    /**
     * Check if the user is an Admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN;
    }

    /**
     * Check if the user is PU_PUSAT.
     */
    public function isPUPusat(): bool
    {
        return $this->role === UserRole::PU_PUSAT;
    }

    public function isPemda(): bool
    {
        return $this->role === UserRole::PEMDA;
    }

    public function isKL(): bool
    {
        return $this->role === UserRole::KL;
    }
}
