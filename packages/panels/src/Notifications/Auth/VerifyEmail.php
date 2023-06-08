<?php

namespace Filament\Notifications\Auth;

use Illuminate\Auth\Notifications\VerifyEmail as BaseNotification;

class VerifyEmail extends BaseNotification
{
    public string $url;

    protected function verificationUrl($notifiable): string
    {
        return $this->url;
    }
}
