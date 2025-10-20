<?php

namespace Filament\Auth\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NoticeOfEmailChangeRequest extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $newEmail,
        public string $blockVerificationUrl,
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
            ->subject(__('filament-panels::auth/notifications/notice-of-email-change-request.subject'))
            ->line(__('filament-panels::auth/notifications/notice-of-email-change-request.lines.0', ['email' => $this->newEmail]))
            ->line(__('filament-panels::auth/notifications/notice-of-email-change-request.lines.1', ['email' => $this->newEmail]))
            ->line(__('filament-panels::auth/notifications/notice-of-email-change-request.lines.2', ['email' => $this->newEmail]))
            ->line(__('filament-panels::auth/notifications/notice-of-email-change-request.lines.3', ['email' => $this->newEmail]))
            ->action(__('filament-panels::auth/notifications/notice-of-email-change-request.action'), $this->blockVerificationUrl);
    }
}
