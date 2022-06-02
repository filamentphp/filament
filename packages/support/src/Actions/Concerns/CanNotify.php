<?php

namespace Filament\Support\Actions\Concerns;

use Closure;
use Filament\Facades\Filament;

trait CanNotify
{
    protected Closure | string | null $notificationMessage = null;

    public function notify(): void
    {
        Filament::notify('success', $this->getNotificationMessage());
    }

    protected function getNotificationMessage(): string
    {
        return $this->evaluate($this->notificationMessage);
    }

    public function notificationMessage(Closure | string $message): static
    {
        $this->notificationMessage = $message;

        return $this;
    }
}
