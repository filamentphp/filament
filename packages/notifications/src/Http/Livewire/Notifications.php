<?php

namespace Filament\Notifications\Http\Livewire;

use Filament\Notifications\Collection;
use Filament\Notifications\Notification;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Notifications extends Component
{
    public Collection $notifications;

    protected $listeners = [
        'notificationSent' => 'add',
    ];

    public function mount(): void
    {
        $this->notifications = new Collection();
    }

    public function add(array $notification): void
    {
        $this->notifications->push(Notification::fromLivewire($notification));
    }

    public function render(): View
    {
        return view('notifications::components.notifications');
    }
}
