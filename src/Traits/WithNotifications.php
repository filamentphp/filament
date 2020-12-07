<?php

namespace Filament\Traits;

trait WithNotifications
{
    public function notify(string $message): void
    {
        $this->dispatchBrowserEvent('notify', $message);
    }
}