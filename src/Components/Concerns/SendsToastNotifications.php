<?php

namespace Filament\Components\Concerns;

trait SendsToastNotifications
{
    public function notify($message)
    {
        $this->dispatchBrowserEvent('notify', $message);
    }
}
