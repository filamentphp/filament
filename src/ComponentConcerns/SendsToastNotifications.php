<?php

namespace Filament\ComponentConcerns;

trait SendsToastNotifications
{
    public function notify($message)
    {
        $this->dispatchBrowserEvent('notify', $message);
    }
}
