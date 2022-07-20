<?php

namespace Filament\Http\Livewire\Concerns;

use Filament\Facades\Filament;
use Filament\Notifications\Notification;

/**
 * @deprecated Use \Filament\Notifications\Http\Livewire\Concerns\CanNotify instead.
 */
trait CanNotify
{
    public function notify(Notification | string $status, ?string $message = null, bool $isAfterRedirect = false): void
    {
        Filament::notify($status, $message);
    }
}
