<?php

namespace Filament\Notifications\Livewire;

use Filament\Notifications\Actions\ActionGroup;
use Filament\Notifications\Collection;
use Filament\Notifications\Notification;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\VerticalAlignment;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\On;
use Livewire\Component;

class Notifications extends Component
{
    private const MAX_NOTIFICATION_ACTION_GROUP_DEPTH = 10;

    private const MAX_NOTIFICATION_NODES = 100;

    // Used to check if Livewire messages should trigger notification animations.
    public bool $isFilamentNotificationsComponent = true;

    public Collection $notifications;

    public static Alignment $alignment = Alignment::Right;

    public static VerticalAlignment $verticalAlignment = VerticalAlignment::Start;

    public static ?string $authGuard = null;

    public function mount(): void
    {
        $this->notifications = new Collection;
        $this->pullNotificationsFromSession();
    }

    #[On('notificationsSent')]
    public function pullNotificationsFromSession(): void
    {
        $notifications = session()->pull('filament.notifications') ?? [];

        if (! is_array($notifications)) {
            return;
        }

        foreach (array_slice($notifications, -self::MAX_NOTIFICATION_NODES) as $notification) {
            $notification = Notification::fromArray($notification);

            $this->pushNotification($notification);
        }
    }

    /**
     * @param  array<string, mixed>  $notification
     */
    #[On('notificationSent')]
    public function pushNotificationFromEvent(array $notification): void
    {
        $notification = Collection::fromLivewire([$notification])->first();

        $this->pushNotification($notification);
    }

    #[On('notificationClosed')]
    public function removeNotification(string $id): void
    {
        if (! $this->notifications->has($id)) {
            return;
        }

        $this->notifications->forget($id);
    }

    /**
     * @param  array<string, mixed>  $notification
     */
    public function handleBroadcastNotification(array $notification): void
    {
        if (($notification['format'] ?? null) !== 'filament') {
            return;
        }

        $notification = Collection::fromLivewire([$notification])->first();

        $this->pushNotification($notification);
    }

    protected function pushNotification(Notification $notification): void
    {
        $notificationNodeCount = $this->getNotificationNodeCount($notification);

        if ($notificationNodeCount === null) {
            return;
        }

        $this->notifications->forget($notification->getId());

        while (($this->getNotificationsNodeCount() + $notificationNodeCount) > self::MAX_NOTIFICATION_NODES) {
            $this->notifications->forget(array_key_first($this->notifications->all()));
        }

        $this->notifications->put(
            $notification->getId(),
            $notification,
        );
    }

    private function getNotificationsNodeCount(): int
    {
        return $this->notifications->sum(
            fn (Notification $notification): int => $this->getNotificationNodeCount($notification) ?? 0,
        );
    }

    private function getNotificationNodeCount(Notification $notification): ?int
    {
        $remainingNodes = self::MAX_NOTIFICATION_NODES - 1;
        $pendingActionLists = [[$notification->getActions(), 0]];

        while ($pendingActionLists !== []) {
            [$actions, $depth] = array_pop($pendingActionLists);

            if (($depth > self::MAX_NOTIFICATION_ACTION_GROUP_DEPTH) || (count($actions) > $remainingNodes)) {
                return null;
            }

            $remainingNodes -= count($actions);

            foreach ($actions as $action) {
                if ($action instanceof ActionGroup) {
                    $pendingActionLists[] = [$action->getActions(), $depth + 1];
                }
            }
        }

        return self::MAX_NOTIFICATION_NODES - $remainingNodes;
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

    public static function alignment(Alignment $alignment): void
    {
        static::$alignment = $alignment;
    }

    public static function verticalAlignment(VerticalAlignment $alignment): void
    {
        static::$verticalAlignment = $alignment;
    }

    public static function authGuard(?string $guard): void
    {
        static::$authGuard = $guard;
    }

    public function render(): View
    {
        return view('filament-notifications::notifications');
    }
}
