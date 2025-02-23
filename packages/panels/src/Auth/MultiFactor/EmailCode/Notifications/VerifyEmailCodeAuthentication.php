<?php

namespace Filament\Auth\MultiFactor\EmailCode\Notifications;

use Exception;
use Filament\Auth\MultiFactor\EmailCode\Contracts\HasEmailCodeAuthentication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerifyEmailCodeAuthentication extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $code,
        public int $codeWindow,
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
        if (! ($notifiable instanceof HasEmailCodeAuthentication)) {
            throw new Exception('The user model must implement the [' . HasEmailCodeAuthentication::class . '] interface to use email code authentication.');
        }

        $expiryMinutes = ceil($this->codeWindow / 2);

        return (new MailMessage)
            ->subject(__('filament-panels::auth/multi-factor/email-code/notifications/verify-email-code-authentication.subject'))
            ->line(trans_choice('filament-panels::auth/multi-factor/email-code/notifications/verify-email-code-authentication.lines.0', $expiryMinutes, ['code' => $this->code, 'minutes' => $expiryMinutes]))
            ->line(trans_choice('filament-panels::auth/multi-factor/email-code/notifications/verify-email-code-authentication.lines.1', $expiryMinutes, ['code' => $this->code, 'minutes' => $expiryMinutes]));
    }
}
