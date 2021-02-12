<?php

namespace Filament\Actions\Concerns;

trait SendsToastNotifications
{
    public function notify($message)
    {
        $this->dispatchBrowserEvent('notify', $message);
    }
}
