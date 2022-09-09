<?php

namespace Filament\Notifications\Http\Livewire;

use Filament\Notifications\Collection;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\DatabaseNotificationCollection;
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

            $this->pushNotification($notification);
        }
    }

    public function removeNotification(string $id): void
    {
        if ($this->notifications->has($id)) {
            $this->notifications->forget($id);
        }

        if (! $this->hasDatabaseNotifications()) {
            return;
        }

        $this->getDatabaseNotificationsQuery()
            ->where('id', $id)
            ->delete();
    }

    public function clearDatabaseNotifications(): void
    {
        $this->getDatabaseNotificationsQuery()->delete();
    }

    public function markAllDatabaseNotificationsAsRead(): void
    {
        $this->getUnreadDatabaseNotificationsQuery()->update(['read_at' => now()]);
    }

    public function getDatabaseNotifications(): DatabaseNotificationCollection
    {
        /** @phpstan-ignore-next-line */
        return $this->getUser()->notifications;
    }

    public function getDatabaseNotificationsQuery(): Builder
    {
        /** @phpstan-ignore-next-line */
        return $this->getUser()->notifications();
    }

    public function getUnreadDatabaseNotificationsQuery(): Builder
    {
        /** @phpstan-ignore-next-line */
        return $this->getUser()->unreadNotifications();
    }

    public function getUnreadDatabaseNotificationsCount(): int
    {
        return $this->getUnreadDatabaseNotificationsQuery()->count();
    }

    public function handleBroadcastNotification($notification): void
    {
        if (! is_array($notification)) {
            return;
        }

        if (! ($notification['id'] ?? null)) {
            return;
        }

        $this->pushNotification(Notification::fromArray($notification));
    }

    protected function pushNotification(Notification $notification): void
    {
        $this->notifications->put(
            $notification->getId(),
            $notification,
        );
    }

    public function hasDatabaseNotifications(): bool
    {
        return $this->getUser() && config('notifications.database.enabled');
    }

    public function getPollingInterval(): ?string
    {
        return config('notifications.database.polling_interval');
    }

    public function getDatabaseNotificationsButton(): ?View
    {
        return null;
    }

    public function getUser(): Model | Authenticatable | null
    {
        return auth()->user();
    }

    public function getBroadcastChannel(): ?string
    {
        $user = $this->getUser();

        if (! $user) {
            return null;
        }

        if (method_exists($user, 'receivesBroadcastNotificationsOn')) {
            return $user->receivesBroadcastNotificationsOn();
        }

        $userClass = str_replace('\\', '.', $user::class);

        return "{$userClass}.{$user->getKey()}";
    }

    public function render(): View
    {
        return view('notifications::notifications');
    }
}
