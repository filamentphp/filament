<?php

namespace Filament\Traits;

trait WithNotifications
{
    public function notify(string $message)
    {
        $this->dispatchBrowserEvent('notify', $message);
    }
}
