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

    public function clear(): void
    {
        auth()->user()->notifications()->delete();
    }

    public function markAllAsRead(): void
    {
        auth()->user()->unreadNotifications()->update(['read_at' => now()]);
    }

    public function removeNotification(string $id): void
    {
        if ($this->notifications->has($id)) {
            $this->notifications->forget($id);

            return;
        }

        $notification = auth()->user()->notifications()->find($id);

        if (! $notification) {
            return;
        }

        $notification->delete();
    }

    public function render(): View
    {
        return view('notifications::notifications');
    }
}
