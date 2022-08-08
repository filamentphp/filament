<?php

namespace Filament\Http\Livewire\Concerns;

use Filament\Facades\Filament;

/**
 * @deprecated Use \Filament\Notifications\Notification::send() instead.
 */
trait CanNotify
{
    protected function getListeners()
    {
        return array_merge($this->listeners, [
            'notify' => 'notify',
        ]);
    }
    
    public function notify(string $status, string $message, bool $isAfterRedirect = false): void
    {
        Filament::notify($status, $message);
    }
}
