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
        'notificationClosed' => 'close',
    ];

    public function mount(): void
    {
        $this->notifications = new Collection();
    }

    public function add(array $notification): void
    {
        $this->notifications->push(Notification::fromLivewire($notification));
    }

    public function close(array $notification): void
    {
        $notification = Notification::fromLivewire($notification);

        $this->notifications->map(
            fn (Notification $i): Notification => $i->getId() === $notification->getId()
                ? $notification->close()
                : $i,
        );
    }

    public function render(): View
    {
        return view('notifications::components.notifications');
    }
}
