<?php

namespace Filament\Models;

use Filament\Traits\HasPackageFactory;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class FilamentUser extends Authenticatable
{
    use HasPackageFactory, Notifiable;

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function sendPasswordResetNotification($token)
    {
        $notification = new ResetPassword($token);
        $notification->createUrlUsing(function ($notifiable, $token) {
            return route('filament.auth.password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ]);
        });

        $this->notify($notification);
    }
}
