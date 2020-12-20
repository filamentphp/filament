<?php

namespace Filament\Traits;

use Illuminate\Auth\Notifications\ResetPassword;

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
}
