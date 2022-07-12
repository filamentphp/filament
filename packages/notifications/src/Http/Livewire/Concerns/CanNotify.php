<?php

namespace Filament\Notifications\Http\Livewire\Concerns;

use Filament\Notifications\Notification;

trait CanNotify
{
    protected function notify(
        Notification | string $status,
        string $message = null,
        bool $isAfterRedirect = false,
    ): void {
        $notification = $status instanceof Notification
            ? $status
            : Notification::make()
                ->title($message)
                ->status($status);

        $this->emit('notificationSent', $notification->toLivewire());
    }
}
