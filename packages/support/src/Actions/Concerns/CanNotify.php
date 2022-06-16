<?php

namespace Filament\Support\Actions\Concerns;

use Closure;

trait CanNotify
{
    protected string | Closure | null $failureNotificationMessage = null;

    protected string | Closure | null $successNotificationMessage = null;

    protected ?Closure $notifyUsing = null;

    public function notifyUsing(?Closure $callback): static
    {
        $this->notifyUsing = $callback;

        return $this;
    }

    public function notify(string | Closure $status, string | Closure $message): void
    {
        if (! $this->notifyUsing) {
            return;
        }

        $this->evaluate($this->notifyUsing, [
            'message' => $this->evaluate($message),
            'status' => $this->evaluate($status),
        ]);
    }

    public function sendFailureNotification(): static
    {
        $message = $this->evaluate($this->failureNotificationMessage);

        if (filled($message)) {
            $this->notify('failure', $message);
        }

        return $this;
    }

    public function sendSuccessNotification(): static
    {
        $message = $this->evaluate($this->successNotificationMessage);

        if (filled($message)) {
            $this->notify('success', $message);
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
