<?php

namespace Filament\Notifications\Livewire;

use Carbon\CarbonInterface;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class DatabaseNotifications extends Component
{
    use WithPagination;

    public static bool $isPaginated = true;

    public static ?string $trigger = null;

    public static ?string $pollingInterval = '30s';

    public static ?string $authGuard = null;

    #[On('databaseNotificationsSent')]
    public function refresh(): void
    {
    }

    #[On('notificationClosed')]
    public function removeNotification(string $id): void
    {
        $this->getNotificationsQuery()
            ->where('id', $id)
            ->delete();
    }

    #[On('markedNotificationAsRead')]
    public function markNotificationAsRead(string $id): void
    {
        $this->getNotificationsQuery()
            ->where('id', $id)
            ->update(['read_at' => now()]);
    }

    #[On('markedNotificationAsUnread')]
    public function markNotificationAsUnread(string $id): void
    {
        $this->getNotificationsQuery()
            ->where('id', $id)
            ->update(['read_at' => null]);
    }

    public function clearNotifications(): void
    {
        $this->getNotificationsQuery()->delete();
    }

    public function markAllNotificationsAsRead(): void
    {
        $this->getUnreadNotificationsQuery()->update(['read_at' => now()]);
    }

    public function getNotifications(): DatabaseNotificationCollection | Paginator
    {
        if (! $this->isPaginated()) {
            /** @phpstan-ignore-next-line */
            return $this->getNotificationsQuery()->get();
        }

        return $this->getNotificationsQuery()->simplePaginate(50);
    }

    public function isPaginated(): bool
    {
        return static::$isPaginated;
    }

    public function getNotificationsQuery(): Builder | Relation
    {
        /** @phpstan-ignore-next-line */
        return $this->getUser()->notifications()->where('data->format', 'filament');
    }

    public function getUnreadNotificationsQuery(): Builder | Relation
    {
        /** @phpstan-ignore-next-line */
        return $this->getNotificationsQuery()->unread();
    }

    public function getUnreadNotificationsCount(): int
    {
        return $this->getUnreadNotificationsQuery()->count();
    }

    public function getPollingInterval(): ?string
    {
        return static::$pollingInterval;
    }

    public function getTrigger(): ?View
    {
        $viewPath = static::$trigger;

        if (blank($viewPath)) {
            return null;
        }

        return view($viewPath);
    }

    public function getUser(): Model | Authenticatable | null
    {
        return auth(static::$authGuard)->user();
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

    public function getNotification(DatabaseNotification $notification): Notification
    {
        return Notification::fromDatabase($notification)
            ->date($this->formatNotificationDate($notification->getAttributeValue('created_at')));
    }

    protected function formatNotificationDate(CarbonInterface $date): string
    {
        return $date->diffForHumans();
    }

    public static function trigger(?string $trigger): void
    {
        static::$trigger = $trigger;
    }

    public static function pollingInterval(?string $interval): void
    {
        static::$pollingInterval = $interval;
    }

    public static function authGuard(?string $guard): void
    {
        static::$authGuard = $guard;
    }

    /**
     * @return array<string>
     */
    public function queryStringHandlesPagination(): array
    {
        return [];
    }

    public function render(): View
    {
        return view('filament-notifications::database-notifications');
    }
}
