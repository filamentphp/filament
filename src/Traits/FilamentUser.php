<?php

namespace Filament\Traits;

use Illuminate\Auth\Notifications\ResetPassword;
use Thomaswelton\LaravelGravatar\Facades\Gravatar;
use Filament;

trait FilamentUser {
    public function sendPasswordResetNotification($token): void
    {
        $notification = new ResetPassword($token);
        $notification->createUrlUsing(function ($notifiable, $token) {
            return route('filament.password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ]);
        });

        $this->notify($notification);
    }

    public function avatar(int $size = 48): string
    {
        if (!$this->avatar) {
            return Gravatar::src($this->email, $size);
        }        

        return Filament::image($this->avatar, [
            'w' => $size,
            'h' => $size,
            'fit' => 'crop',
        ]);
    }
}
