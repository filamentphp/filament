<?php

namespace Filament\Listeners\Auth;

use Filament\Facades\Filament;
use Filament\Notifications\Auth\VerifyEmail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification as BaseListener;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class SendEmailVerificationNotification extends BaseListener
{
    public function handle(Registered $event): void
    {
        if (! $event->user instanceof MustVerifyEmail) {
            return;
        }

        if ($event->user->hasVerifiedEmail()) {
            return;
        }

        $notification = new VerifyEmail();
        $notification->url = Filament::getVerifyEmailUrl($event->user);

        $event->user->notify($notification);
    }
}
