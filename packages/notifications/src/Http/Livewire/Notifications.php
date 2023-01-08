<?php

namespace Filament\Notifications\Http\Livewire;

use Carbon\CarbonInterface;
use Filament\Notifications\Collection;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Livewire\Component;

class Notifications extends Component
{
    public Collection $notifications;

    /**
     * @var array<string, string>
     */
    protected $listeners = [
        'notificationSent' => 'pushNotificationFromEvent',
        'notificationsSent' => 'pullNotificationsFromSession',
        'notificationClosed' => 'removeNotification',
    ];

    public static bool $hasDatabaseNotifications = false;

    public static ?string $databaseNotificationsTrigger = null;

    public static ?string $databaseNotificationsPollingInterval = '30s';

    public static string $horizontalAlignment = 'right';

    public static string $verticalAlignment = 'top';

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

    /**
     * @param  array<string, mixed>  $notification
     */
    public function pushNotificationFromEvent(array $notification): void
    {
        $notification = Notification::fromArray($notification);

        $this->pushNotification($notification);
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
        return $this->getDatabaseNotificationsQuery()->get();
    }

    public function getDatabaseNotificationsQuery(): Builder | Relation
    {
        /** @phpstan-ignore-next-line */
        return $this->getUser()->notifications()->where('data->format', 'filament');
    }

    public function getUnreadDatabaseNotificationsQuery(): Builder | Relation
    {
        /** @phpstan-ignore-next-line */
        return $this->getDatabaseNotificationsQuery()->unread();
    }

    public function getUnreadDatabaseNotificationsCount(): int
    {
        return $this->getUnreadDatabaseNotificationsQuery()->count();
    }

    /**
     * @param  array<string, mixed>  $notification
     */
    public function handleBroadcastNotification(array $notification): void
    {
        if (($notification['format'] ?? null) !== 'filament') {
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
        return $this->getUser() && static::$hasDatabaseNotifications;
    }

    public function getPollingInterval(): ?string
    {
        return static::$databaseNotificationsPollingInterval;
    }

    public function getDatabaseNotificationsTrigger(): ?View
    {
        $viewPath = static::$databaseNotificationsTrigger;

        if (blank($viewPath)) {
            return null;
        }

        return view($viewPath);
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

    public function getNotificationFromDatabaseRecord(DatabaseNotification $notification): Notification
    {
        return Notification::fromDatabase($notification)
            ->date($this->formatNotificationDate($notification->getAttributeValue('created_at')));
    }

    protected function formatNotificationDate(CarbonInterface $date): string
    {
        return $date->diffForHumans();
    }

    public static function databaseNotifications(bool $condition = true): void
    {
        static::$hasDatabaseNotifications = $condition;
    }

    public static function databaseNotificationsTrigger(?string $trigger): void
    {
        static::$databaseNotificationsTrigger = $trigger;
    }

    public static function databaseNotificationsPollingInterval(?string $interval): void
    {
        static::$databaseNotificationsPollingInterval = $interval;
    }

    public static function horizontalAlignment(string $alignment): void
    {
        static::$horizontalAlignment = $alignment;
    }

    public static function verticalAlignment(string $alignment): void
    {
        static::$verticalAlignment = $alignment;
    }

    public function render(): View
    {
        return view('filament-notifications::notifications');
    }
}
