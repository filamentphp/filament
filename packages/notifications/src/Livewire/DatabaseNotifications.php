<?php

namespace Filament\Notifications\Livewire;

use Carbon\CarbonInterface;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Notifications\Notification;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Support\Attributes\Exposed;
use Filament\Support\Attributes\ServerOnly;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class DatabaseNotifications extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use WithPagination;

    public static bool $isPaginated = true;

    public static ?string $trigger = null;

    public static ?string $pollingInterval = '30s';

    public static ?string $authGuard = null;

    #[Exposed]
    #[On('databaseNotificationsSent')]
    public function refresh(): void {}

    #[Exposed]
    #[On('notificationClosed')]
    public function removeNotification(string $id): void
    {
        if (! Str::isUuid($id)) {
            return;
        }

        $this->getNotificationsQuery()
            ->where('id', $id)
            ->delete();
    }

    #[Exposed]
    #[On('markedNotificationAsRead')]
    public function markNotificationAsRead(string $id): void
    {
        if (! Str::isUuid($id)) {
            return;
        }

        $this->getNotificationsQuery()
            ->where('id', $id)
            ->update(['read_at' => now()]);
    }

    #[Exposed]
    #[On('markedNotificationAsUnread')]
    public function markNotificationAsUnread(string $id): void
    {
        if (! Str::isUuid($id)) {
            return;
        }

        $this->getNotificationsQuery()
            ->where('id', $id)
            ->update(['read_at' => null]);
    }

    #[Exposed]
    public function clearNotifications(): void
    {
        $this->getNotificationsQuery()->delete();
    }

    #[Exposed]
    public function markAllNotificationsAsRead(): void
    {
        $this->getUnreadNotificationsQuery()->update(['read_at' => now()]);
    }

    #[ServerOnly]
    public function getNotifications(): DatabaseNotificationCollection | Paginator
    {
        if (! $this->isPaginated()) {
            /** @phpstan-ignore-next-line */
            return $this->getNotificationsQuery()->get();
        }

        return $this->getNotificationsQuery()->simplePaginate(50, pageName: 'database-notifications-page');
    }

    #[ServerOnly]
    public function isPaginated(): bool
    {
        return static::$isPaginated;
    }

    #[ServerOnly]
    public function getNotificationsQuery(): Builder | Relation
    {
        $user = $this->getUser();

        if (! $user) {
            abort(401);
        }

        /** @phpstan-ignore-next-line */
        return $user->notifications()->where('data->format', 'filament');
    }

    #[ServerOnly]
    public function getUnreadNotificationsQuery(): Builder | Relation
    {
        /** @phpstan-ignore-next-line */
        return $this->getNotificationsQuery()->unread();
    }

    #[ServerOnly]
    public function getUnreadNotificationsCount(): int
    {
        return $this->getUnreadNotificationsQuery()->count();
    }

    #[ServerOnly]
    public function getPollingInterval(): ?string
    {
        return static::$pollingInterval;
    }

    #[ServerOnly]
    public function getTrigger(): ?View
    {
        $viewPath = static::$trigger;

        if (blank($viewPath)) {
            return null;
        }

        return view($viewPath);
    }

    #[ServerOnly]
    public function markAllNotificationsAsReadAction(): Action
    {
        return Action::make('markAllNotificationsAsRead')
            ->link()
            ->label(__('filament-notifications::database.modal.actions.mark_all_as_read.label'))
            ->action('markAllNotificationsAsRead');
    }

    #[ServerOnly]
    public function clearNotificationsAction(): Action
    {
        return Action::make('clearNotifications')
            ->link()
            ->color('danger')
            ->label(__('filament-notifications::database.modal.actions.clear.label'))
            ->action('clearNotifications')
            ->close();
    }

    #[ServerOnly]
    public function getUser(): Model | Authenticatable | null
    {
        return auth(static::$authGuard)->user();
    }

    #[ServerOnly]
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

    #[ServerOnly]
    public function getNotification(DatabaseNotification $notification): Notification
    {
        return Notification::fromDatabase($notification)
            ->date($this->formatNotificationDate($notification->getAttributeValue('created_at')));
    }

    protected function formatNotificationDate(CarbonInterface $date): string
    {
        return $date->diffForHumans();
    }

    #[ServerOnly]
    public static function trigger(?string $trigger): void
    {
        static::$trigger = $trigger;
    }

    #[ServerOnly]
    public static function pollingInterval(?string $interval): void
    {
        static::$pollingInterval = $interval;
    }

    #[ServerOnly]
    public static function authGuard(?string $guard): void
    {
        static::$authGuard = $guard;
    }

    /**
     * @return array<string>
     */
    #[ServerOnly]
    public function queryStringHandlesPagination(): array
    {
        return [];
    }

    #[ServerOnly]
    public function render(): View
    {
        return view('filament-notifications::database-notifications');
    }

    #[ServerOnly]
    public function placeholder(): string
    {
        return '<div>' . $this->getTrigger()?->with(['unreadNotificationsCount' => $this->getUnreadNotificationsCount()])->render() . '</div>';
    }
}
