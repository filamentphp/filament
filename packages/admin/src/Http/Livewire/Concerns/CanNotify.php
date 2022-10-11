<?php

namespace Filament\Http\Livewire\Concerns;

use Filament\Facades\Filament;

/**
 * @deprecated Use \Filament\Notifications\Notification::send() instead.
 */
trait CanNotify
{
    public function notify(string $status, string $message): void
    {
        Filament::notify($status, $message);
    }
}
