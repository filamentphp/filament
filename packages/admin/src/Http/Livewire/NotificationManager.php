<?php

namespace Filament\Http\Livewire;

use Livewire\Component;

class NotificationManager extends Component
{
    public function dropNotificationsFromSession()
    {
        session()->pull('notifications');
    }

    public function render()
    {
        return view('filament::components.notification-manager');
    }
}
