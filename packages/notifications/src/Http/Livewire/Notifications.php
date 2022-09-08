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
        'notificationsSent' => 'pullNotificationsFromSession',
        'notificationClosed' => 'removeNotification',
    ];

    public function mount(): void
    {
        $this->notifications = new Collection();
        $this->pullNotificationsFromSession();
    }

    public function pullNotificationsFromSession(): void
    {
        foreach (session()->pull('filament.notifications') ?? [] as $notification) {
            $notification = Notification::fromArray($notification);

            $this->notifications->put(
                $notification->getId(),
                $notification,
            );
        }
    }

    public function removeNotification(string $id): void
    {
        $this->notifications->forget($id);
    }

    public function render(): View
    {
        return view('notifications::notifications');
    }
}
