<?php

use Filament\Facades\Filament;
use Filament\Livewire\DatabaseNotifications as PanelDatabaseNotifications;
use Filament\Livewire\Notifications as PanelNotifications;
use Filament\Notifications\Events\DatabaseNotificationsSent;
use Filament\Notifications\Livewire\DatabaseNotifications;
use Filament\Notifications\Livewire\Notifications;
use Filament\Notifications\Notification;
use Filament\Support\Enums\IconSize;
use Filament\Support\Icons\Heroicon;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

use function Filament\Tests\livewire;

uses(TestCase::class);

dataset('database notification components', [
    'standalone' => [DatabaseNotifications::class],
    'panel' => [PanelDatabaseNotifications::class],
]);

beforeEach(function (): void {
    $this->user = User::factory()->create();

    $this->actingAs($this->user);
});

describe('ignores IDs that are not database notification IDs', function (): void {
    it('does not affect database notifications when `notificationClosed` is dispatched', function (): void {
        Notification::make()->title('Test')->sendToDatabase($this->user);
        $flashNotification = Notification::make('password_reset_link_sent')->send();

        livewire(DatabaseNotifications::class)
            ->dispatch('notificationClosed', id: $flashNotification->getId());

        expect($this->user->notifications()->count())->toBe(1);
    });

    it('does not affect database notifications when `markedNotificationAsRead` is dispatched', function (): void {
        Notification::make()->title('Test')->sendToDatabase($this->user);
        $flashNotification = Notification::make('password_reset_link_sent')->send();

        livewire(DatabaseNotifications::class)
            ->dispatch('markedNotificationAsRead', id: $flashNotification->getId());

        expect($this->user->notifications()->first()->read_at)->toBeNull();
    });

    it('does not affect database notifications when `markedNotificationAsUnread` is dispatched', function (): void {
        Notification::make()->title('Test')->sendToDatabase($this->user);
        $this->user->notifications()->first()->markAsRead();
        $flashNotification = Notification::make('password_reset_link_sent')->send();

        livewire(DatabaseNotifications::class)
            ->dispatch('markedNotificationAsUnread', id: $flashNotification->getId());

        expect($this->user->notifications()->first()->read_at)->not->toBeNull();
    });
});

describe('acts on matching database notification IDs', function (): void {
    it('deletes the matching database notification when `notificationClosed` is dispatched', function (): void {
        Notification::make()->title('Test')->sendToDatabase($this->user);
        $notification = $this->user->notifications()->first();

        livewire(DatabaseNotifications::class)
            ->dispatch('notificationClosed', id: $notification->getKey());

        expect($this->user->notifications()->count())->toBe(0);
    });

    it('marks the matching database notification as read when `markedNotificationAsRead` is dispatched', function (): void {
        Notification::make()->title('Test')->sendToDatabase($this->user);
        $notification = $this->user->notifications()->first();

        livewire(DatabaseNotifications::class)
            ->dispatch('markedNotificationAsRead', id: $notification->getKey());

        expect($this->user->notifications()->first()->read_at)->not->toBeNull();
    });

    it('marks the matching database notification as unread when `markedNotificationAsUnread` is dispatched', function (): void {
        Notification::make()->title('Test')->sendToDatabase($this->user);
        $notification = $this->user->notifications()->first();
        $notification->markAsRead();

        livewire(DatabaseNotifications::class)
            ->dispatch('markedNotificationAsUnread', id: $notification->getKey());

        expect($this->user->notifications()->first()->read_at)->toBeNull();
    });
});

describe('user isolation', function (): void {
    beforeEach(function (): void {
        Schema::create('database_notification_users', function (Blueprint $table): void {
            $table->id();
            $table->timestamps();
        });

        $this->otherUser = DatabaseNotificationsOtherUser::query()->create(['id' => $this->user->getKey()]);
        $this->sameModelUser = DatabaseNotificationsOtherUser::query()->create(['id' => $this->otherUser->getKey() + 1]);

        config()->set('auth.guards.notifications', ['driver' => 'session', 'provider' => 'notification-users']);
        config()->set('auth.providers.notification-users', ['driver' => 'eloquent', 'model' => DatabaseNotificationsOtherUser::class]);
        auth('notifications')->setUser($this->otherUser);

        // The default guard remains authenticated as a different model with the same key.
        auth()->shouldUse('web');
        DatabaseNotifications::authGuard('notifications');
        Filament::getCurrentOrDefaultPanel()->authGuard('notifications');

        foreach ([$this->user, $this->otherUser, $this->sameModelUser] as $user) {
            Notification::make()->title('Private notification for ' . $user::class . ':' . $user->getKey())->sendToDatabase($user);
        }

        $this->foreignNotification = $this->user->notifications()->first();
        $this->ownedNotification = $this->otherUser->notifications()->first();
        $this->nonFilamentNotification = $this->otherUser->notifications()->create([
            'id' => (string) str()->uuid(),
            'type' => 'other-format',
            'data' => ['title' => 'Not a Filament notification'],
        ]);
    });

    afterEach(function (): void {
        DatabaseNotifications::authGuard(null);
        DatabaseNotifications::$isPaginated = true;
        Notifications::authGuard(null);
    });

    it('scopes rendering, counts, and hydrated refreshes to the current guard user', function (string $component, bool $isPaginated): void {
        DatabaseNotifications::$isPaginated = $isPaginated;
        Notification::make()->title('Second private notification')->sendToDatabase($this->sameModelUser);

        if ($component === PanelDatabaseNotifications::class) {
            // Panel authentication must take precedence over standalone configuration.
            DatabaseNotifications::authGuard('web');
        }

        $livewire = livewire($component)
            ->assertSee($this->ownedNotification->data['title'])
            ->assertDontSee($this->foreignNotification->data['title'])
            ->assertDontSee($this->sameModelUser->notifications()->first()->data['title'])
            ->assertDontSee($this->nonFilamentNotification->data['title']);

        expect($livewire->instance()->getUnreadNotificationsCount())->toBe(1)
            ->and($livewire->instance()->getNotifications()->pluck('id')->all())->toBe([$this->ownedNotification->getKey()]);

        auth('notifications')->setUser($this->sameModelUser);

        $livewire->dispatch('databaseNotificationsSent')
            ->assertSee($this->sameModelUser->notifications()->first()->data['title'])
            ->assertDontSee($this->ownedNotification->data['title'])
            ->assertDontSee($this->foreignNotification->data['title']);

        expect($livewire->instance()->getUnreadNotificationsCount())->toBe(2);
    })->with('database notification components')->with([true, false]);

    it('only mutates an owned Filament notification through individual events', function (string $component, string $event): void {
        if ($event === 'markedNotificationAsUnread') {
            DatabaseNotification::query()->update(['read_at' => now()->subDay()]);
        }

        $before = DatabaseNotification::query()->orderBy('id')->get()->toArray();
        auth('notifications')->setUser($this->user);
        $livewire = livewire($component);
        auth('notifications')->setUser($this->otherUser);

        foreach ([$this->foreignNotification, $this->sameModelUser->notifications()->first(), $this->nonFilamentNotification] as $notification) {
            $livewire->dispatch($event, id: $notification->getKey());
        }

        expect(DatabaseNotification::query()->orderBy('id')->get()->toArray())->toBe($before);

        $livewire->dispatch($event, id: $this->ownedNotification->getKey());

        if ($event === 'notificationClosed') {
            expect($this->ownedNotification->fresh())->toBeNull();
        } else {
            expect($this->ownedNotification->fresh()->read_at === null)->toBe($event === 'markedNotificationAsUnread');
        }

        expect(DatabaseNotification::query()->whereKeyNot($this->ownedNotification->getKey())->orderBy('id')->get()->toArray())
            ->toBe(array_values(array_filter($before, fn (array $notification): bool => $notification['id'] !== $this->ownedNotification->getKey())));
    })->with('database notification components')->with([
        'notificationClosed', 'markedNotificationAsRead', 'markedNotificationAsUnread',
    ]);

    it('scopes bulk actions to the current user after hydration', function (string $component, string $action): void {
        if ($action === 'markAllNotificationsAsRead') {
            Notification::make()->title('Already read')->sendToDatabase($this->otherUser);
            $this->otherUser->notifications()->whereKeyNot($this->ownedNotification->getKey())
                ->where('data->format', 'filament')->update(['read_at' => now()->subDay()]);
        }

        auth('notifications')->setUser($this->user);
        $livewire = livewire($component);
        auth('notifications')->setUser($this->otherUser);

        $before = DatabaseNotification::query()->whereKeyNot($this->ownedNotification->getKey())->orderBy('id')->get()->toArray();

        $livewire->call($action)
            ->assertDontSee($this->foreignNotification->data['title'])
            ->assertDontSee($this->nonFilamentNotification->data['title']);

        if ($action === 'clearNotifications') {
            expect($this->ownedNotification->fresh())->toBeNull();
        } else {
            expect($this->ownedNotification->fresh()->read_at)->not->toBeNull();
        }

        expect(DatabaseNotification::query()->whereKeyNot($this->ownedNotification->getKey())->orderBy('id')->get()->toArray())->toBe($before);
    })->with('database notification components')->with(['clearNotifications', 'markAllNotificationsAsRead']);

    it('does not return another user\'s notification data through `getNotification()`', function (string $component): void {
        // Implicit model binding is not ownership-scoped. The returned `Notification`
        // serializes to `{}`, decoded as `[]`, without exposing its protected payload.
        livewire($component)
            ->call('getNotification', $this->foreignNotification->getKey())
            ->assertReturned([])
            ->assertDontSee($this->foreignNotification->data['title']);
    })->with('database notification components');

    it('rejects unauthenticated rendering and hydrated mutations without changing rows', function (string $component, string $method): void {
        $livewire = livewire($component);
        $before = DatabaseNotification::query()->orderBy('id')->get()->toArray();
        auth('notifications')->logout();

        $livewire->call($method, $this->ownedNotification->getKey())
            ->assertStatus(401)
            ->assertDontSee($this->ownedNotification->data['title'])
            ->assertDontSee($this->foreignNotification->data['title']);
        livewire($component)->assertStatus(401)
            ->assertDontSee($this->ownedNotification->data['title'])
            ->assertDontSee($this->foreignNotification->data['title']);

        expect(DatabaseNotification::query()->orderBy('id')->get()->toArray())->toBe($before);
    })->with('database notification components')->with([
        'removeNotification', 'markNotificationAsRead', 'markNotificationAsUnread',
        'clearNotifications', 'markAllNotificationsAsRead', '$refresh',
    ]);

    it('resolves matching private broadcast channels without confusing guards or model keys', function (string $component): void {
        Notifications::authGuard('notifications');

        if (in_array($component, [PanelDatabaseNotifications::class, PanelNotifications::class])) {
            DatabaseNotifications::authGuard('web');
            Notifications::authGuard('web');
        }

        $livewire = livewire($component);
        $expectedChannel = 'DatabaseNotificationsOtherUser.' . $this->otherUser->getKey();

        expect($livewire->instance()->getBroadcastChannel())->toBe($expectedChannel)
            ->and((new DatabaseNotificationsSent($this->otherUser))->broadcastOn())->toBe('private-' . $expectedChannel)
            ->and((new DatabaseNotificationsSent($this->user))->broadcastOn())->toBe('private-Filament.Tests.Fixtures.Models.User.' . $this->user->getKey());

        $customChannelUser = DatabaseNotificationsCustomChannelUser::query()->findOrFail($this->otherUser->getKey());
        auth('notifications')->setUser($customChannelUser);
        $livewire->call('$refresh');

        expect($livewire->instance()->getBroadcastChannel())->toBe('custom.notifications.' . $customChannelUser->getKey())
            ->and((new DatabaseNotificationsSent($customChannelUser))->broadcastOn())->toBe('private-custom.notifications.' . $customChannelUser->getKey());

        auth('notifications')->logout();

        expect($livewire->instance()->getBroadcastChannel())->toBeNull();
    })->with([DatabaseNotifications::class, PanelDatabaseNotifications::class, Notifications::class, PanelNotifications::class]);
});

describe('browser interactions', function (): void {
    beforeEach(function (): void {
        Artisan::call('filament:assets');
    });

    it('focuses the slide-over window instead of the `Mark all as read` header action when opened', function (): void {
        retry(10, function (): void {
            $user = User::factory()->create();
            $this->actingAs($user);

            Notification::make()
                ->title('First')
                ->icon(Heroicon::Bell)
                ->iconSize(IconSize::Small)
                ->sendToDatabase($user);
            Notification::make()->title('Second')->sendToDatabase($user);

            visit('/database-notifications-browser-test')
                ->click('[data-testid="database-notifications-trigger"]')
                ->assertVisible('[id="database-notifications"] .fi-modal-window')
                ->assertVisible('[id="database-notifications"] .fi-no-notification-icon.fi-size-sm')
                ->wait(0.5)
                // The focus trap focuses the modal window itself, not the `Mark all as read` action, which `Enter` would immediately trigger.
                ->assertScript('document.activeElement === document.querySelector(\'[id="database-notifications"] .fi-modal-window\')', true)
                // The header actions remain in the tab order.
                ->assertScript('document.querySelector(\'[id="database-notifications"] .fi-modal-header .fi-ac button\').tabIndex', 0)
                ->assertNoSmoke()
                ->assertNoAccessibilityIssues();

            // No notification has been marked as read by simply opening the slide-over.
            expect($user->unreadNotifications()->count())->toBe(2);

            visit('/database-notifications-browser-test')
                ->inDarkMode()
                ->click('[data-testid="database-notifications-trigger"]')
                ->assertVisible('[id="database-notifications"] .fi-modal-window')
                ->assertVisible('[id="database-notifications"] .fi-no-notification-icon.fi-size-sm')
                ->assertNoSmoke()
                ->assertNoAccessibilityIssues();
        });
    });
});

class DatabaseNotificationsOtherUser extends User
{
    protected $table = 'database_notification_users';
}

class DatabaseNotificationsCustomChannelUser extends DatabaseNotificationsOtherUser
{
    public function receivesBroadcastNotificationsOn(): string
    {
        return 'custom.notifications.' . $this->getKey();
    }
}
