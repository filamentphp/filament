<?php

namespace Filament\Notifications\Auth;

use Illuminate\Auth\Notifications\VerifyEmail as BaseNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class VerifyEmail extends BaseNotification implements ShouldQueue
{
    use Queueable;

    public string $url;

    protected function verificationUrl($notifiable): string
    {
        return $this->url;
    }
}
