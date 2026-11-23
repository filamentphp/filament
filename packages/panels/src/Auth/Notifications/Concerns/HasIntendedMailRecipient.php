<?php

namespace Filament\Auth\Notifications\Concerns;

use Illuminate\Support\Collection;

trait HasIntendedMailRecipient
{
    // `null` retains the delivery behavior of notifications queued before recipient binding was added.
    protected ?string $intendedMailRecipientHash = null;

    /**
     * @param  object  $notifiable
     */
    protected function captureIntendedMailRecipient($notifiable): void
    {
        $this->intendedMailRecipientHash = $this->getMailRecipientHash($notifiable);
    }

    /**
     * @param  object  $notifiable
     * @param  string  $channel
     */
    public function shouldSend($notifiable, $channel): bool
    {
        if (($channel !== 'mail') || ($this->intendedMailRecipientHash === null)) {
            return true;
        }

        return hash_equals($this->intendedMailRecipientHash, $this->getMailRecipientHash($notifiable));
    }

    /**
     * @param  object  $notifiable
     */
    protected function getMailRecipientHash($notifiable): string
    {
        $recipients = $notifiable->routeNotificationFor('mail', $this);

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
