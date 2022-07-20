<?php

namespace Filament\Notifications\Http\Livewire\Concerns;

use Filament\Facades\Filament;
use Filament\Notifications\Notification;

trait CanNotify
{
    protected function notify(
        Notification | string $notification,
        string $message = null,
        bool $isAfterRedirect = false,
    ): void {
        Filament::notify($notification, $message);
    }
}
