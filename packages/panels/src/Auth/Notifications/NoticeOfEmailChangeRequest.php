<?php

namespace Filament\Auth\Notifications;

use Filament\Auth\Notifications\Concerns\HasIntendedMailRecipient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class NoticeOfEmailChangeRequest extends Notification implements ShouldQueue
{
    use HasIntendedMailRecipient;
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
        $this->captureIntendedMailRecipient($notifiable);

        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject(__('filament-panels::auth/notifications/notice-of-email-change-request.subject'));

        foreach (range(0, 3) as $lineIndex) {
            $line = __("filament-panels::auth/notifications/notice-of-email-change-request.lines.{$lineIndex}", ['email' => $this->newEmail]);

            // HTML paragraphs bypass Markdown. Protect backslashes and line breaks from renderer normalization.
            $message->line(new HtmlString('<p>' . strtr(e($line), [
                '\\' => '&#92;',
                "\r" => '&#13;',
                "\n" => '&#10;',
            ]) . '</p>'));
        }

        return $message->action(__('filament-panels::auth/notifications/notice-of-email-change-request.action'), $this->blockVerificationUrl);
    }
}
