<?php

namespace Filament\Support\Actions\Concerns;

use Closure;

trait CanNotify
{
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

    public function sendSuccessNotification(): static
    {
        $message = $this->evaluate($this->successNotificationMessage);

        if (filled($message)) {
            $this->notify('success', $message);
        }

        return $this;
    }

    public function successNotification(string | Closure | null $message): static
    {
        $this->successNotificationMessage = $message;

        return $this;
    }
}
