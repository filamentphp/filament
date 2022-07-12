<?php

namespace Filament\Notifications\Http\Livewire\Concerns;

use Filament\Notifications\Notification;

trait CanNotify
{
    protected function notify(Notification $notification): void
    {
        $this->emit('notificationSent', $notification);
    }
}
