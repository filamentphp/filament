<?php

namespace Filament\Notifications\Livewire;

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
        foreach (session()->pull('filament.notifications') ?? [] as $notification) {
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
        $notification = Notification::fromArray($notification);

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

        $this->pushNotification(Notification::fromArray($notification));
    }

    protected function pushNotification(Notification $notification): void
    {
        $this->notifications->put(
            $notification->getId(),
            $notification,
        );
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
