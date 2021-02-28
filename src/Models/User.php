<?php

namespace Filament\Models;

use Filament\Database\Factories\UserFactory;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\URL;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;

    protected $casts = [
        'roles' => 'array',
    ];

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $table = 'filament_users';

    protected static function newFactory()
    {
        return UserFactory::new();
    }

    public function can($resource, $route = 'index')
    {
        return $resource::authorizationManager()->can($route, $this);
    }

    public function hasRole($role)
    {
        return in_array($role, $this->roles);
    }

    public function sendPasswordResetNotification($token)
    {
        $notification = new ResetPasswordNotification($token);
        $notification->createUrlUsing(function ($notifiable, $token) {
            return URL::signedRoute(
                'filament.auth.password.reset',
                [
                    'email' => $notifiable->getEmailForPasswordReset(),
                    'token' => $token,
                ],
                now()->addMinutes(config('auth.passwords.filament_users.expire')),
            );
        });

        $this->notify($notification);
    }
}
