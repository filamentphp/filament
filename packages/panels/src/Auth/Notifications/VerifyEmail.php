<?php

namespace Filament\Auth\Notifications;

use Filament\Auth\Notifications\Concerns\HasIntendedMailRecipient;
use Illuminate\Auth\Notifications\VerifyEmail as BaseNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class VerifyEmail extends BaseNotification implements ShouldQueue
{
    use HasIntendedMailRecipient;
    use Queueable;

    public string $url;

    /**
     * @return array<string> | string
     */
    public function via($notifiable)
    {
        $this->captureIntendedMailRecipient($notifiable);

        return parent::via($notifiable);
    }

    protected function verificationUrl($notifiable): string
    {
        return $this->url;
    }
}
