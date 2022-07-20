<?php

namespace Filament\Notifications\Http\Livewire\Concerns;

use Filament\Notifications\Facades\Notification as NotificationFacade;
use Filament\Notifications\Notification;

trait CanNotify
{
    protected function notify(Notification $notification): void
    {
        NotificationFacade::send($notification);
    }
}
