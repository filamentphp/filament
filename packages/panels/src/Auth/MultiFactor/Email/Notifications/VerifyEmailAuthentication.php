<?php

namespace Filament\Auth\MultiFactor\Email\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerifyEmailAuthentication extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $code,
        public int $codeExpiryMinutes,
    ) {}

    /**
     * @return array<string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('filament-panels::auth/multi-factor/email/notifications/verify-email-authentication.subject'))
            ->line(trans_choice('filament-panels::auth/multi-factor/email/notifications/verify-email-authentication.lines.0', $this->codeExpiryMinutes, ['code' => $this->code, 'minutes' => $this->codeExpiryMinutes]))
            ->line(trans_choice('filament-panels::auth/multi-factor/email/notifications/verify-email-authentication.lines.1', $this->codeExpiryMinutes, ['code' => $this->code, 'minutes' => $this->codeExpiryMinutes]));
    }
}
