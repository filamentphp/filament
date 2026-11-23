<?php

namespace Filament\Auth\Notifications\Concerns;

use Illuminate\Support\Collection;

/**
 * @internal Never use this trait. It is not part of the public API and may change in minor and patch releases.
 */
trait HasIntendedMailRecipient
{
    // `null` retains the delivery behavior of notifications queued before recipient binding was added.
    protected ?string $intendedMailRecipientHash = null;

    protected function captureIntendedMailRecipient(object $notifiable): void
    {
        $this->intendedMailRecipientHash = $this->getMailRecipientHash($notifiable);
    }

    /**
     * @return bool
     */
    public function shouldSend(object $notifiable, string $channel)
    {
        if (($channel !== 'mail') || ($this->intendedMailRecipientHash === null)) {
            return true;
        }

        return hash_equals($this->intendedMailRecipientHash, $this->getMailRecipientHash($notifiable));
    }

    protected function getMailRecipientHash(object $notifiable): string
    {
        $recipients = $notifiable->routeNotificationFor('mail', $this) ?: [];

        if (is_string($recipients)) {
            $recipients = [$recipients];
        }

        $recipients = (new Collection($recipients))
            ->map(static function ($recipient, $email): string {
                if (! is_numeric($email)) {
                    return (string) $email;
                }

                return is_string($recipient) ? $recipient : (string) $recipient->email;
            })
            ->sort()
            ->values()
            ->all();

        return hash('sha256', serialize($recipients));
    }
}
