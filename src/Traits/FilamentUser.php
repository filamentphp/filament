<?php

namespace Filament\Traits;

use Illuminate\Auth\Notifications\ResetPassword;

trait FilamentUser {
    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token) {
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