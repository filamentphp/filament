<?php

namespace Filament\Support\Actions\Concerns;

use Closure;
use Filament\Notifications\Notification;

trait CanNotify
{
    protected string | Closure | null $failureNotificationMessage = null;

    protected string | Closure | null $successNotificationMessage = null;

    public function sendFailureNotification(): static
    {
        $message = $this->evaluate($this->failureNotificationMessage);

        if (filled($message)) {
            Notification::make()
                ->title($message)
                ->danger()
                ->send();
        }

        return $this;
    }

    public function sendSuccessNotification(): static
    {
        $message = $this->evaluate($this->successNotificationMessage);

        if (filled($message)) {
            Notification::make()
                ->title($message)
                ->success()
                ->send();
        }

        return $this;
    }

    public function failureNotificationMessage(string | Closure | null $message): static
    {
        $this->failureNotificationMessage = $message;

        return $this;
    }

    public function successNotificationMessage(string | Closure | null $message): static
    {
        $this->successNotificationMessage = $message;

        return $this;
    }
}
