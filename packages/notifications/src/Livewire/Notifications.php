<?php

namespace Filament\Notifications\Livewire;

use Filament\Actions\ActionGroup;
use Filament\Notifications\Collection;
use Filament\Notifications\Notification;
use Filament\Notifications\NotificationMailbox;
use Filament\Support\Components\Attributes\Exposed;
use Filament\Support\Components\Attributes\Unexposed;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\VerticalAlignment;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\Livewire;

class Notifications extends Component
{
    private const MAX_NOTIFICATION_NODES = 100;

    private const MAX_NOTIFICATION_PULL_WORK = 50;

    private const MAX_NOTIFICATION_ACTION_GROUP_DEPTH = 10;

    // Used to check if Livewire messages should trigger notification animations.
    #[Locked]
    public bool $isFilamentNotificationsComponent = true;

    #[Locked]
    public Collection $notifications;

    #[Locked]
    public bool $hasPulledNotificationsOnMount = false;

    public static Alignment $alignment = Alignment::Right;

    public static VerticalAlignment $verticalAlignment = VerticalAlignment::Start;

    public static ?string $authGuard = null;

    #[Unexposed]
    public function mount(): void
    {
        $this->notifications = new Collection;
        $request = request();

        if (
            $request->hasSession() &&
            $request->isMethod('GET') &&
            ($request->header('Sec-Fetch-Dest') === 'document') &&
            (! Livewire::isLivewireRequest()) &&
            (! $request->headers->has('X-Livewire-Navigate')) &&
            (! $request->prefetch()) &&
            (! str_contains(strtolower($request->header('Sec-Purpose', '')), 'prefetch'))
        ) {
            $mailbox = app(NotificationMailbox::class);
            $mailbox->flush();
            $notifications = $mailbox->tryPull();

            if ($notifications === null) {
                return;
            }

            foreach ($notifications as $notification) {
                $this->pushNotification(Notification::fromArray($notification));
            }

            $this->hasPulledNotificationsOnMount = true;
        }
    }

    /**
     * @param  array<string>  $batches
     *
     * @deprecated Use `pullNotifications()` instead.
     */
    #[Exposed]
    public function pullNotificationsFromSession(array $batches = []): void
    {
        $this->pullNotifications($batches);
    }

    /** @param array<string> $batches */
    #[Exposed]
    #[On('notificationsSent')]
    public function pullNotifications(array $batches = []): void
    {
        abort_if(count($batches) > self::MAX_NOTIFICATION_PULL_WORK, 419);

        abort_unless($this->areBatchIdentifiersStrings($batches), 419);

        $pullWork = max(count($batches), 1);
        $remainingPullWork = request()->attributes->getInt('filament.notifications.remainingPullWork', self::MAX_NOTIFICATION_PULL_WORK);

        abort_if($pullWork > $remainingPullWork, 419);

        request()->attributes->set('filament.notifications.remainingPullWork', $remainingPullWork - $pullWork);

        validator(['batches' => $batches], [
            'batches' => ['array', 'max:50'],
            'batches.*' => ['required', 'string', 'regex:/\A[a-f0-9]{40}\z/'],
        ])->validate();

        foreach (app(NotificationMailbox::class)->pull($batches) as $notification) {
            $notification = Notification::fromArray($notification);

            $this->pushNotification($notification);
        }
    }

    /**
     * @param  array<string, mixed>  $notification
     */
    #[Exposed]
    #[On('notificationSent')]
    public function pushNotificationFromEvent(array $notification): void
    {
        $notification = Collection::fromLivewire([$notification])->first();

        $this->pushNotification($notification);
    }

    #[Exposed]
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
    #[Exposed]
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
            $this->notifications->shift();
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

    /** @param array<mixed> $batches */
    private function areBatchIdentifiersStrings(array $batches): bool
    {
        foreach ($batches as $batch) {
            if (! is_string($batch)) {
                return false;
            }
        }

        return true;
    }

    #[Unexposed]
    public function getUser(): Model | Authenticatable | null
    {
        return auth(static::$authGuard)->user();
    }

    #[Unexposed]
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

    #[Unexposed]
    public static function alignment(Alignment $alignment): void
    {
        static::$alignment = $alignment;
    }

    #[Unexposed]
    public static function verticalAlignment(VerticalAlignment $alignment): void
    {
        static::$verticalAlignment = $alignment;
    }

    #[Unexposed]
    public static function authGuard(?string $guard): void
    {
        static::$authGuard = $guard;
    }

    #[Unexposed]
    public function render(): View
    {
        return view('filament-notifications::notifications');
    }
}
