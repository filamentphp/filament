<?php

namespace Filament\Models;

use Filament\Database\Factories\FilamentUserFactory;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\URL;

class FilamentUser extends Authenticatable
{
    use HasFactory;
    use Notifiable;

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function can($resource, $route = 'index')
    {
        return $resource::authorizationManager()->can($route, $this);
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

    protected static function newFactory()
    {
        return FilamentUserFactory::new();
    }
}
