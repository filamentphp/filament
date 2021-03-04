<?php

namespace Filament\Models;

use Filament\Database\Factories\UserFactory;
use Filament\Models\Concerns\IsFilamentUser;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\URL;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory;
    use IsFilamentUser;
    use Notifiable;

    public static $filamentAdminColumn = 'is_admin';

    public static $filamentRolesColumn = 'roles';

    protected $casts = [
        'is_admin' => 'bool',
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
