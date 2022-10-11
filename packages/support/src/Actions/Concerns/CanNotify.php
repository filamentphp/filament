<?php

namespace Filament\Support\Actions\Concerns;

use Closure;
use Filament\Notifications\Notification;

trait CanNotify
{
    protected Notification | Closure | null $failureNotification = null;

    protected Notification | Closure | null $successNotification = null;

    public function sendFailureNotification(): static
    {
        $this->evaluate($this->failureNotification)?->send();

        return $this;
    }

    public function failureNotification(Notification | Closure | null $notification): static
    {
        $this->failureNotification = $notification;

        return $this;
    }

    /**
     * @deprecated Use `failureNotificationTitle()` instead.
     */
    public function failureNotificationMessage(string | Closure | null $message): static
    {
        $title = $this->evaluate($message);

        if (blank($title)) {
            return $this;
        }

        return $this->failureNotification(
            Notification::make()
                ->danger()
                ->title($title),
        );
    }

    public function failureNotificationTitle(string | Closure | null $title): static
    {
        return $this->failureNotificationMessage($title);
    }

    public function sendSuccessNotification(): static
    {
        $this->evaluate($this->successNotification)?->send();

        return $this;
    }

    public function successNotification(Notification | Closure | null $notification): static
    {
        $this->successNotification = $notification;

        return $this;
    }

    /**
     * @deprecated Use `successNotificationTitle()` instead.
     */
    public function successNotificationMessage(string | Closure | null $message): static
    {
        $title = $this->evaluate($message);

        if (blank($title)) {
            return $this;
        }

        return $this->successNotification(
            Notification::make()
                ->success()
                ->title($title),
        );
    }

    public function successNotificationTitle(string | Closure | null $title): static
    {
        return $this->successNotificationMessage($title);
    }
}
