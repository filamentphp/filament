<?php

namespace Filament\Models;

use Filament\Traits\HasPackageFactory;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\URL;

class FilamentUser extends Authenticatable
{
    use HasPackageFactory, Notifiable;

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function can($resource, $route = 'index')
    {
        return $resource::authorizationManager()->can($resource, $route);
    }

    public function hasRole($role)
    {
        return FilamentRoleUser::where([
            ['role_id', $role],
            ['user_id', $this->attributes['id']],
        ])->exists();
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
