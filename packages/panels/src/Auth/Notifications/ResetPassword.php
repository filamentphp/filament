<?php

namespace Filament\Auth\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class ResetPassword extends BaseNotification implements ShouldQueue
{
    use Queueable;

    public string $url;

    protected function resetUrl($notifiable): string
    {
        return $this->url;
    }
}
