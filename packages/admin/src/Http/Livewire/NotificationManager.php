<?php

namespace Filament\Http\Livewire;

use Livewire\Component;

class NotificationManager extends Component
{
    public function getNotificationsProperty()
    {
        return session()->remove('notifications') ?? [];
    }

    public function render()
    {
        return view('filament::pages.notification-manager');
    }
}
