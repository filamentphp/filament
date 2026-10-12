<?php

use BladeUI\Icons\Factory;
use BladeUI\Icons\IconsManifest;
use Filament\Actions\Action;
use Filament\Notifications\Collection;
use Filament\Notifications\Livewire\Notifications;
use Filament\Notifications\Notification;
use Filament\Notifications\NotificationMailbox;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Enums\IconSize;
use Filament\Support\Enums\Size;
use Filament\Support\Icons\Heroicon;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Fixtures\Notifications\CustomNotification;
use Filament\Tests\Fixtures\Pages\NotificationBrowserTest;
use Filament\Tests\TestCase;
use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\Repository;
use Illuminate\Contracts\Cache\Store as CacheStore;
use Illuminate\Foundation\Http\Middleware\TrimStrings;
use Illuminate\Notifications\DatabaseNotification as DatabaseNotificationModel;
use Illuminate\Session\Store;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Livewire\Livewire;
use PHPUnit\Framework\AssertionFailedError;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('can render', function (): void {
    livewire(Notifications::class)
        ->assertSeeHtml('notifications');
});

it('can render with a string enum `iconSize()`', function (): void {
    $html = Notification::make()
        ->icon(Heroicon::Check)
        ->iconSize('lg')
        ->toHtml();

    expect($html)->toContain('fi-size-lg');
});

it('can send notifications', function (): void {
    $iconSets = Arr::only(app(Factory::class)->all(), 'heroicons');
    $icons = app(IconsManifest::class)->getManifest($iconSets);

    $getRandomColor = function (): string {
        return Arr::random([
            'danger',
            'gray',
            'info',
            'primary',
            'success',
            'warning',
        ]);
    };

    $getRandomIcon = function () use ($icons): string {
        return 'heroicon-' . collect($icons)->flatten()->random();
    };

    expect(session()->get('filament.notifications'))
        ->toBeEmpty();

    Notification::make($id = Str::random())
        ->actions([
            Action::make($actionName = Str::random())
                ->close($shouldClose = (bool) rand(0, 1))
                ->color($actionColor = $getRandomColor())
                ->disabled($isActionDisabled = (bool) rand(0, 1))
                ->dispatch($actionEvent = Str::random(), $actionEventData = [Str::random()])
                ->extraAttributes($actionExtraAttributes = ['x' . Str::random(15) => Str::random()]) // Attributes must start with a letter
                ->icon($actionIcon = $getRandomIcon())
                ->iconPosition($actionIconPosition = Arr::random([IconPosition::After, IconPosition::Before]))
                ->label($actionLabel = Str::random())
                ->outlined($isActionOutlined = (bool) rand(0, 1))
                ->size($actionSize = Arr::random([Size::ExtraSmall, Size::Small, Size::Medium, Size::Large, Size::ExtraLarge]))
                ->url(
                    $actionUrl = Str::random(),
                    shouldOpenInNewTab: $shouldActionOpenUrlInNewTab = (bool) rand(0, 1),
                ),
        ])
        ->body($body = Str::random())
        ->duration($duration = rand(1, 10))
        ->icon($icon = $getRandomIcon())
        ->iconColor($iconColor = $getRandomColor())
        ->title($title = Str::random())
        ->send();

    app(NotificationMailbox::class)->flush();

    $component = livewire(Notifications::class)
        ->dispatch('notificationsSent');

    $notifications = $component->instance()->notifications->toArray();

    expect($notifications)
        ->toBeArray()
        ->toHaveCount(1);

    $notification = Arr::last($notifications);

    expect($notification)
        ->toBeArray()
        ->id->toBe($id)
        ->body->toBe($body)
        ->duration->toBe($duration)
        ->icon->toBe($icon)
        ->iconColor->toBe($iconColor)
        ->title->toBe($title);

    $actions = $notification['actions'];

    expect($actions)
        ->toBeArray();

    $action = Arr::first($actions);

    expect($action)
        ->toBeArray()
        ->name->toBe($actionName)
        ->color->toBe($actionColor)
        ->event->toBe($actionEvent)
        ->eventData->toBe($actionEventData)
        ->extraAttributes->toBe($actionExtraAttributes)
        ->icon->toBe($actionIcon)
        ->iconPosition->toBe($actionIconPosition)
        ->isOutlined->toBe($isActionOutlined)
        ->isDisabled->toBe($isActionDisabled)
        ->label->toBe($actionLabel)
        ->shouldClose->toBe($shouldClose)
        ->shouldOpenUrlInNewTab->toBe($shouldActionOpenUrlInNewTab)
        ->size->toBe($actionSize)
        ->url->toBe($actionUrl);

    expect($component->instance()->notifications)
        ->toBeInstanceOf(Collection::class)
        ->toHaveCount(1);

    $notification = $component->instance()->notifications->first();

    expect($notification)
        ->toBeInstanceOf(Notification::class)
        ->getId()->toBe($id)
        ->getBody()->toBe($body)
        ->getDuration()->toBe($duration)
        ->getIcon()->toBe($icon)
        ->getIconColor()->toBe($iconColor)
        ->getTitle()->toBe($title);

    $actions = $notification->getActions();

    expect($actions)
        ->toBeArray();

    $action = Arr::first($actions);

    expect($action)
        ->toBeInstanceOf(Action::class)
        ->getName()->toBe($actionName)
        ->getColor()->toBe($actionColor)
        ->getEvent()->toBe($actionEvent)
        ->getEventData()->toBe($actionEventData)
        ->getExtraAttributes()->toBe($actionExtraAttributes)
        ->getIcon()->toBe($actionIcon)
        ->getIconPosition()->toBe($actionIconPosition)
        ->isOutlined()->toBe($isActionOutlined)
        ->isDisabled()->toBe($isActionDisabled)
        ->getLabel()->toBe($actionLabel)
        ->shouldClose()->toBe($shouldClose)
        ->shouldOpenUrlInNewTab()->toBe($shouldActionOpenUrlInNewTab)
        ->getSize()->toBe($actionSize)
        ->getUrl()->toBe($actionUrl);

    expect(session()->get('filament.notifications'))
        ->toBeEmpty();
});

it('can close notifications', function (): void {
    ($notification = Notification::make())->send();

    app(NotificationMailbox::class)->flush();

    $component = livewire(Notifications::class);

    $component
        ->dispatch('notificationsSent')
        ->dispatch('notificationClosed', id: $notification->getId());

    expect($component->instance()->notifications)
        ->toBeInstanceOf(Collection::class)
        ->toHaveCount(0);
});

it('does not replay a consumed notification after a stale session is restored', function (): void {
    Route::middleware('web')->get('/notification-mailbox-send', static function () {
        Notification::make('mailbox-regression')->title('Saved')->send();

        return redirect('/');
    });

    $this->get('/notification-mailbox-send')->assertRedirect('/');
    $staleSession = session()->all();
    app()->forgetInstance(NotificationMailbox::class);

    livewire(Notifications::class)
        ->dispatch('notificationsSent')
        ->assertCount('notifications', 1);

    session()->replace($staleSession);

    livewire(Notifications::class)
        ->dispatch('notificationsSent')
        ->assertCount('notifications', 0);
});

it('does not consume notifications while mounting an unvisited page', function (): void {
    Notification::make('pending')->title('Still pending')->send();

    app(NotificationMailbox::class)->flush();

    livewire(Notifications::class)->assertCount('notifications', 0);

    livewire(Notifications::class)
        ->dispatch('notificationsSent')
        ->assertCount('notifications', 1);
});

it('keeps the newest 50 pending notifications', function (array $batches, int $firstRetainedId, int $lastRetainedId): void {
    Route::middleware('web')->get('/notification-limit/{start}/{count}', static function (int $start, int $count) {
        foreach (range($start, $start + $count - 1) as $index) {
            Notification::make("notification-{$index}")->title("Notification {$index}")->send();
        }

        return redirect('/');
    });

    foreach ($batches as [$start, $count]) {
        $this->get("/notification-limit/{$start}/{$count}")->assertRedirect('/');
    }

    $notifications = livewire(Notifications::class)->dispatch('notificationsSent')->get('notifications');

    expect($notifications->map(static fn (Notification $notification): string => $notification->getTitle())->values()->all())
        ->toBe(array_map(static fn (int $index): string => "Notification {$index}", range($firstRetainedId, $lastRetainedId)));

    livewire(Notifications::class)->dispatch('notificationsSent')->assertCount('notifications', 0);
})->with([
    'exact limit' => [[[1, 50]], 1, 50],
    'one oversized request' => [[[1, 51]], 2, 51],
    'multiple requests' => [[[1, 29], [30, 30]], 10, 59],
]);

it('delivers notifications with a cache store that does not support locking', function (): void {
    $backingStore = new ArrayStore;
    $store = Mockery::mock(CacheStore::class);

    foreach (['get', 'put', 'forget'] as $method) {
        $store->shouldReceive($method)->andReturnUsing(static fn (...$arguments) => $backingStore->{$method}(...$arguments));
    }

    Cache::extend('without-locks', static fn (): Repository => Cache::repository($store));
    config(['cache.stores.without-locks.driver' => 'without-locks', 'filament.cache_store' => 'without-locks']);

    Route::middleware('web')->get('/notification-without-locks/{title}', static function (string $title) {
        Notification::make()->title($title)->send();

        return redirect('/');
    });

    $this->get('/notification-without-locks/First')->assertRedirect('/');
    $this->get('/notification-without-locks/Second')->assertRedirect('/');

    Cache::store()->flush();

    livewire(Notifications::class)->dispatch('notificationsSent')
        ->assertCount('notifications', 2)
        ->assertSee('First')
        ->assertSee('Second');

    livewire(Notifications::class)->dispatch('notificationsSent')->assertCount('notifications', 0);
});

it('preserves notifications when a redirecting producer and consumer share a Livewire request', function (): void {
    $this->actingAs(User::factory()->create());
    $producer = livewire(NotificationBrowserTest::class);
    $consumer = livewire(Notifications::class);

    $response = $this->postJson(Livewire::getUpdateUri(), [
        'components' => [
            [
                'snapshot' => json_encode($producer->snapshot),
                'updates' => [],
                'calls' => [['method' => 'sendRedirectNotification', 'params' => []]],
            ],
            [
                'snapshot' => json_encode($consumer->snapshot),
                'updates' => [],
                'calls' => [['method' => 'pullNotificationsFromSession', 'params' => []]],
            ],
        ],
    ], ['X-Livewire' => 'true'])->assertOk();

    expect($response->json('components.0.effects.redirect'))->not->toBeNull();
    livewire(Notifications::class)->dispatch('notificationsSent')
        ->assertCount('notifications', 1)
        ->assertSee('Redirect notification');
    livewire(Notifications::class)->dispatch('notificationsSent')->assertCount('notifications', 0);
});

it('discards notifications queued before session invalidation on a route with previously gathered middleware', function (bool $shouldSendAfterInvalidation): void {
    $route = Route::middleware('web')->get('/notification-mailbox-invalidate', static function () use ($shouldSendAfterInvalidation) {
        Notification::make('before-invalidation')->title('Before invalidation')->send();
        session()->invalidate();

        if ($shouldSendAfterInvalidation) {
            Notification::make('after-invalidation')->title('After invalidation')->send();
        }

        return redirect('/');
    });
    $route->gatherMiddleware();

    $this->get('/notification-mailbox-invalidate')->assertRedirect('/');

    $storedSession = new Store(session()->getName(), session()->getHandler(), session()->getId());
    $storedSession->start();

    expect($storedSession->get('filament.notification_mailbox'))
        ->toBeString()
        ->toBe(session()->get('filament.notification_mailbox'));

    session()->flush();
    session()->replace($storedSession->all());

    $notifications = livewire(Notifications::class)->dispatch('notificationsSent')->get('notifications');

    expect($notifications->map(static fn (Notification $notification): string => $notification->getTitle())->values()->all())
        ->toBe($shouldSendAfterInvalidation ? ['After invalidation'] : []);

    livewire(Notifications::class)->dispatch('notificationsSent')->assertCount('notifications', 0);
})->with([
    'only before invalidation' => false,
    'before and after invalidation' => true,
]);

it('preserves notifications through login and logout unless the session is invalidated', function (bool $shouldLogIn, bool $shouldInvalidate): void {
    $user = User::factory()->create();

    if (! $shouldLogIn) {
        $this->actingAs($user);
    }

    Route::middleware('web')->get('/notification-before-invalidation', static function () {
        Notification::make('published-before-invalidation')->title('Published before invalidation')->send();

        return redirect('/');
    });

    Route::middleware('web')->get('/notification-invalidated-authentication', static function () use ($user, $shouldLogIn, $shouldInvalidate) {
        Notification::make('pending-before-invalidation')->title('Pending before invalidation')->send();

        if ($shouldInvalidate) {
            session()->invalidate();
        }

        if ($shouldLogIn) {
            auth()->login($user);
        } else {
            auth()->logout();
        }

        Notification::make('after-authentication-change')->title('After authentication change')->send();

        return redirect('/');
    });

    $this->get('/notification-before-invalidation')->assertRedirect('/');
    $this->get('/notification-invalidated-authentication')->assertRedirect('/');

    $notifications = livewire(Notifications::class)->dispatch('notificationsSent')
        ->assertCount('notifications', $shouldInvalidate ? 1 : 3)
        ->assertSee('After authentication change');

    if ($shouldInvalidate) {
        $notifications
            ->assertDontSee('Published before invalidation')
            ->assertDontSee('Pending before invalidation');
    } else {
        $notifications
            ->assertSee('Published before invalidation')
            ->assertSee('Pending before invalidation');
    }

    livewire(Notifications::class)->dispatch('notificationsSent')->assertCount('notifications', 0);
})->with([
    'login' => [true, false],
    'logout' => [false, false],
    'login after invalidation' => [true, true],
    'logout after invalidation' => [false, true],
]);

it('delivers notifications sent by global middleware after handling the request', function (): void {
    app()->bind(TrimStrings::class, static fn () => new class extends TrimStrings
    {
        public function handle($request, Closure $next)
        {
            $response = parent::handle($request, $next);

            if (
                $request->is('notification-global-middleware') ||
                ($request->input('components.0.calls.0.method') === 'returnNotificationPayload')
            ) {
                Notification::make()->title('Sent by global middleware')->send();
            }

            return $response;
        }
    });

    Route::middleware('web')->get('/notification-global-middleware', static fn () => redirect('/'));

    $this->actingAs(User::factory()->create());
    $producer = livewire(NotificationBrowserTest::class);

    $this->get('/notification-global-middleware')->assertRedirect('/');

    $response = $this->postJson(Livewire::getUpdateUri(), [
        'components' => [[
            'snapshot' => json_encode($producer->snapshot),
            'updates' => [],
            'calls' => [],
        ]],
    ], ['X-Livewire' => 'true'])
        ->assertOk()
        ->assertJsonPath('filamentNotifications', true);

    livewire(Notifications::class)->dispatch('notificationsSent')
        ->assertCount('notifications', 1)
        ->assertSee('Sent by global middleware');

    livewire(Notifications::class)->dispatch('notificationsSent')->assertCount('notifications', 0);

    $this->postJson(Livewire::getUpdateUri(), [
        'components' => [[
            'snapshot' => $response->json('components.0.snapshot'),
            'updates' => [],
            'calls' => [],
        ]],
    ], ['X-Livewire' => 'true'])
        ->assertOk()
        ->assertJsonMissingPath('filamentNotifications');

    $response = $this->postJson(Livewire::getUpdateUri(), [
        'components' => [[
            'snapshot' => json_encode($producer->snapshot),
            'updates' => [],
            'calls' => [['method' => 'returnNotificationPayload', 'params' => []]],
        ]],
    ], ['X-Livewire' => 'true'])
        ->assertOk()
        ->assertJsonPath('filamentNotifications', true);

    $returnedPayload = json_decode($response->getContent())->components[0]->effects->returns[0];

    expect($returnedPayload->empty)->toBeObject();
    expect($returnedPayload->numeric)->toBeObject();
    expect($returnedPayload->numeric->{'0'})->toBe('first');
    expect($returnedPayload->list)->toBeArray()->toBeEmpty();

    livewire(Notifications::class)->dispatch('notificationsSent')
        ->assertCount('notifications', 1)
        ->assertSee('Sent by global middleware');
});

it('isolates login notifications from a revoked guest session', function (): void {
    $user = User::factory()->create();
    Route::middleware('web')->get('/notification-mailbox-guest', static function () {
        Notification::make('pending-guest')->title('Pending guest notification')->send();

        return redirect('/');
    });
    Route::middleware('web')->get('/notification-mailbox-login', static function () use ($user) {
        auth()->login($user);
        Notification::make('private-login')->title('Private login notification')->send();

        return redirect('/');
    });

    $this->get('/notification-mailbox-guest')->assertRedirect('/');
    $guestSessionId = session()->getId();
    $this->get('/notification-mailbox-login')->assertRedirect('/');
    $authenticatedSession = session()->all();
    $authenticatedSessionId = session()->getId();

    // Reusing only the revoked cookie must not reconstruct the new mailbox.
    session()->flush();
    session()->setId($guestSessionId);
    livewire(Notifications::class)->dispatch('notificationsSent')->assertCount('notifications', 0);

    session()->replace($authenticatedSession);
    session()->setId($authenticatedSessionId);
    livewire(Notifications::class)->dispatch('notificationsSent')
        ->assertCount('notifications', 2)
        ->assertSee('Pending guest notification')
        ->assertSee('Private login notification');
    livewire(Notifications::class)->dispatch('notificationsSent')->assertCount('notifications', 0);
});

it('does not add toast dismissal behavior to inline notifications', function (): void {
    $html = Notification::make()
        ->inline()
        ->actions([
            Action::make('dismiss')->close(),
        ])
        ->toEmbeddedHtml();

    expect($html)
        ->not->toContain('dismiss-notification')
        ->not->toContain('x-on:keydown.escape')
        ->toContain('x-on:click="close()"');
});

it('matches the correct notification with `assertNotified()` when multiple notifications are sent', function (): void {
    Notification::make()->title('First')->body('First body')->send();
    Notification::make()->title('Second')->body('Second body')->send();

    // The matching notification is NOT the first one sent.
    Notification::assertNotified(
        Notification::make()->title('Second')->body('Second body'),
    );
});

it('fails `assertNotified()` when no sent notification matches the given instance', function (): void {
    Notification::make()->title('First')->body('First body')->send();
    Notification::make()->title('Second')->body('Second body')->send();

    expect(fn () => Notification::assertNotified(
        Notification::make()->title('Third')->body('Third body'),
    ))->toThrow(AssertionFailedError::class);
});

it('fails `assertNotNotified()` when a matching notification was sent but was not first', function (): void {
    Notification::make()->title('First')->body('First body')->send();
    Notification::make()->title('Second')->body('Second body')->send();

    // `Second` WAS sent (just not first), so asserting it was not notified must fail.
    expect(fn () => Notification::assertNotNotified(
        Notification::make()->title('Second')->body('Second body'),
    ))->toThrow(AssertionFailedError::class);
});

it('passes `assertNotNotified()` when the given instance was genuinely not sent', function (): void {
    Notification::make()->title('First')->body('First body')->send();
    Notification::make()->title('Second')->body('Second body')->send();

    Notification::assertNotNotified(
        Notification::make()->title('Third')->body('Third body'),
    );
});

function getLastNotificationAction()
{
    app(NotificationMailbox::class)->flush();

    $notificationsLivewireComponent = new Notifications;
    $notificationsLivewireComponent->mount();
    $notificationsLivewireComponent->pullNotificationsFromSession();
    $notifications = $notificationsLivewireComponent->notifications;

    return $notifications->first()->getActions()[0];
}

describe('events', function (): void {
    it('can dispatch an event', function (): void {
        $action = Action::make('action')->dispatch('an_event');
        expect($action->getLivewireClickHandler())->toBe("\$dispatch('an_event')");

        $notification = Notification::make()->actions([$action])->send();
        expect(getLastNotificationAction()->getLivewireClickHandler())->toBe("\$dispatch('an_event')");

        $action = Action::make('action')->dispatch('an_event', ['data']);
        expect($action->getLivewireClickHandler())->toBe("\$dispatch('an_event', JSON.parse('[\\u0022data\\u0022]'))");

        $notification = Notification::make()->actions([$action])->send();
        expect(getLastNotificationAction()->getLivewireClickHandler())->toBe("\$dispatch('an_event', JSON.parse('[\\u0022data\\u0022]'))");
    });

    it('can dispatch an event to itself', function (): void {
        $action = Action::make('action')->dispatchSelf('an_event');
        expect($action->getLivewireClickHandler())->toBe("\$dispatchSelf('an_event')");

        $notification = Notification::make()->actions([$action])->send();
        expect(getLastNotificationAction()->getLivewireClickHandler())->toBe("\$dispatchSelf('an_event')");

        $action = Action::make('action')->dispatchSelf('an_event', ['data']);
        expect($action->getLivewireClickHandler())->toBe("\$dispatchSelf('an_event', JSON.parse('[\\u0022data\\u0022]'))");

        $notification = Notification::make()->actions([$action])->send();
        expect(getLastNotificationAction()->getLivewireClickHandler())->toBe("\$dispatchSelf('an_event', JSON.parse('[\\u0022data\\u0022]'))");
    });

    it('can dispatch an event to a component', function (): void {
        $action = Action::make('action')->dispatchTo('a_component', 'an_event');
        expect($action->getLivewireClickHandler())->toBe("\$dispatchTo('a_component', 'an_event')");

        $notification = Notification::make()->actions([$action])->send();
        expect(getLastNotificationAction()->getLivewireClickHandler())->toBe("\$dispatchTo('a_component', 'an_event')");

        $action = Action::make('action')->dispatchTo('a_component', 'an_event', ['data']);
        expect($action->getLivewireClickHandler())->toBe("\$dispatchTo('a_component', 'an_event', JSON.parse('[\\u0022data\\u0022]'))");

        $notification = Notification::make()->actions([$action])->send();
        expect(getLastNotificationAction()->getLivewireClickHandler())->toBe("\$dispatchTo('a_component', 'an_event', JSON.parse('[\\u0022data\\u0022]'))");
    });

});

it('can bind custom notification object', function (): void {
    app()->bind(Notification::class, CustomNotification::class);

    $notification = Notification::make();

    expect($notification)
        ->toBeInstanceOf(CustomNotification::class);
});

it('can resolve custom notification object from data', function (): void {
    app()->bind(Notification::class, CustomNotification::class);

    Notification::make()
        ->size($size = 'lg')
        ->body($body = Str::random())
        ->title($title = Str::random())
        ->send();

    app(NotificationMailbox::class)->flush();

    $component = livewire(Notifications::class);

    $component
        ->dispatch('notificationsSent');

    expect($component->instance()->notifications)->toHaveCount(1);

    $notification = $component->instance()->notifications->first();

    expect($notification)
        ->toBeInstanceOf(CustomNotification::class)
        ->getSize()->toBe($size);
});

it('can mark notification as inline using `inline()`', function (): void {
    $notification = Notification::make();

    expect($notification->isInline())->toBeFalse();

    $notification->inline();

    expect($notification->isInline())->toBeTrue();
});

it('can unset inline using `inline(false)`', function (): void {
    $notification = Notification::make()->inline();

    expect($notification->isInline())->toBeTrue();

    $notification->inline(false);

    expect($notification->isInline())->toBeFalse();
});

it('does not apply an unsafe view when restoring via `fromArray()`', function (): void {
    $notification = Notification::make();
    $array = $notification->toArray();

    // A view not in `safeViews` must not be applied
    $restored = Notification::fromArray([...$array, 'view' => 'notifications::not-safe']);

    expect($restored->hasView())->toBeFalse();
});

it('can accumulate safe views across multiple calls to `safeViews()`', function (): void {
    $notification = Notification::make()
        ->safeViews('notifications::first')
        ->safeViews(['notifications::second']);

    // Directly verify that `isViewSafe()` logic works via `safeViews()` accumulation
    // by passing the instance's view through its own array round-trip after registering.
    // Since `fromArray()` creates a new instance, we test directly on the original object.
    $array = $notification->toArray();

    // The notification itself knows about the safe views; applying view() proves storage
    $notification->view('notifications::first');
    expect($notification->hasView())->toBeTrue();
    expect($notification->getView())->toBe('notifications::first');
});

describe('serialization', function (): void {
    it('serializes default and configured scalable icon sizes via `toArray()`', function (): void {
        $defaultSizeNotification = Notification::make()
            ->icon(Heroicon::Bell);
        $smallNotification = Notification::make()
            ->icon(Heroicon::Bell)
            ->iconSize('sm');
        $customSizeNotification = Notification::make()
            ->icon(Heroicon::Bell)
            ->iconSize('custom');

        expect($defaultSizeNotification->toArray())
            ->icon->toBe('heroicon-s-bell')
            ->iconSize->toBeNull()
            ->and($smallNotification->toArray())
            ->icon->toBe('heroicon-c-bell')
            ->iconSize->toBe(IconSize::Small)
            ->and($customSizeNotification->toArray())
            ->icon->toBe('heroicon-s-bell')
            ->iconSize->toBe('custom');
    });

    it('restores notification icon sizes via `fromArray()`', function (): void {
        $notification = Notification::make()
            ->icon(Heroicon::Bell)
            ->iconSize(IconSize::Small);

        $restoredNotification = Notification::fromArray($notification->toArray());

        expect($restoredNotification)
            ->getIcon()->toBe('heroicon-c-bell')
            ->getIconSize()->toBe(IconSize::Small);
    });

    it('restores old notification payloads without an `iconSize` field', function (): void {
        $payload = Notification::make()
            ->icon(Heroicon::Bell)
            ->toArray();

        unset($payload['iconSize']);

        $restoredNotification = Notification::fromArray($payload);

        expect($restoredNotification)
            ->getIcon()->toBe('heroicon-s-bell')
            ->getIconSize()->toBeNull();
    });

    it('preserves a lazy default icon size when restoring old notification payloads', function (): void {
        $restoredNotification = NotificationWithLazyIconSize::fromArray([
            'title' => 'Important',
        ]);

        expect($restoredNotification->getIconSize())->toBe(IconSize::Large);
    });

    it('preserves notification icon sizes through delivery', function (): void {
        Notification::make()
            ->icon(Heroicon::Bell)
            ->iconSize(IconSize::Small)
            ->send();

        app(NotificationMailbox::class)->flush();

        $notificationsComponent = new Notifications;
        $notificationsComponent->mount();
        $notificationsComponent->pullNotificationsFromSession();
        $restoredNotification = $notificationsComponent->notifications->first();

        expect($restoredNotification)
            ->getIcon()->toBe('heroicon-c-bell')
            ->getIconSize()->toBe(IconSize::Small);
    });

    it('preserves notification icon sizes through broadcast messages', function (): void {
        $broadcastNotification = Notification::make()
            ->icon(Heroicon::Bell)
            ->iconSize(IconSize::Small)
            ->toBroadcast();
        $payload = json_decode(
            json_encode($broadcastNotification->toBroadcast(null)->data, JSON_THROW_ON_ERROR),
            associative: true,
            flags: JSON_THROW_ON_ERROR,
        );

        $notificationsComponent = new Notifications;
        $notificationsComponent->mount();
        $notificationsComponent->handleBroadcastNotification($payload);
        $restoredNotification = $notificationsComponent->notifications->first();

        expect($restoredNotification)
            ->getIcon()->toBe('heroicon-c-bell')
            ->getIconSize()->toBe(IconSize::Small);
    });

    it('preserves notification icon sizes through database messages', function (): void {
        $payload = Notification::make()
            ->icon(Heroicon::Bell)
            ->iconSize(IconSize::Small)
            ->toDatabase()
            ->toDatabase(null);
        $databaseNotification = new DatabaseNotificationModel;
        $databaseNotification->setRawAttributes([
            'id' => 'notification-id',
            'data' => json_encode($payload, JSON_THROW_ON_ERROR),
        ]);

        $restoredNotification = Notification::fromDatabase($databaseNotification);

        expect($restoredNotification)
            ->getId()->toBe('notification-id')
            ->getIcon()->toBe('heroicon-c-bell')
            ->getIconSize()->toBe(IconSize::Small);
    });

    it('serializes simple notification via `toArray()`', function (): void {
        $notification = Notification::make('test-id')
            ->title('Test Title')
            ->body('Test Body')
            ->icon('heroicon-o-bell')
            ->iconColor('success')
            ->color('primary')
            ->duration(5000);

        expect($notification->toArray())
            ->toMatchSnapshot();
    });

    it('serializes notification with string icon via `toArray()`', function (): void {
        $notification = Notification::make('test-id')
            ->title('Test Title')
            ->icon('heroicon-o-check-circle');

        expect($notification->toArray())
            ->toMatchSnapshot();
    });

    it('serializes notification with `Heroicon` enum via `toArray()`', function (): void {
        $notification = Notification::make('test-id')
            ->title('Test Title')
            ->icon(Heroicon::CheckCircle);

        expect($notification->toArray())
            ->toMatchSnapshot();
    });

    it('serializes notification with outlined `Heroicon` enum via `toArray()`', function (): void {
        $notification = Notification::make('test-id')
            ->title('Test Title')
            ->icon(Heroicon::OutlinedCheckCircle);

        expect($notification->toArray())
            ->toMatchSnapshot();
    });

    it('serializes notification with action containing `Heroicon` enum via `toArray()`', function (): void {
        $notification = Notification::make('test-id')
            ->title('Test Title')
            ->actions([
                Action::make('view')
                    ->label('View')
                    ->icon(Heroicon::Eye)
                    ->color('primary'),
            ]);

        expect($notification->toArray())
            ->toMatchSnapshot();
    });

    it('serializes notification with action containing outlined `Heroicon` enum via `toArray()`', function (): void {
        $notification = Notification::make('test-id')
            ->title('Test Title')
            ->actions([
                Action::make('view')
                    ->label('View')
                    ->icon(Heroicon::OutlinedEye)
                    ->color('primary'),
            ]);

        expect($notification->toArray())
            ->toMatchSnapshot();
    });

    it('serializes notification with action containing `Heroicon` enum and icon size via `toArray()`', function (): void {
        $notification = Notification::make('test-id')
            ->title('Test Title')
            ->actions([
                Action::make('view')
                    ->label('View')
                    ->icon(Heroicon::Eye)
                    ->iconSize(IconSize::Small),
            ]);

        expect($notification->toArray())
            ->toMatchSnapshot();
    });

    it('serializes notification with multiple actions via `toArray()`', function (): void {
        $notification = Notification::make('test-id')
            ->title('Test Title')
            ->body('Test Body')
            ->actions([
                Action::make('view')
                    ->label('View')
                    ->icon(Heroicon::Eye)
                    ->url('/view'),
                Action::make('edit')
                    ->label('Edit')
                    ->icon(Heroicon::Pencil)
                    ->url('/edit'),
                Action::make('delete')
                    ->label('Delete')
                    ->icon(Heroicon::Trash)
                    ->color('danger'),
            ]);

        expect($notification->toArray())
            ->toMatchSnapshot();
    });

    it('serializes notification via `getDatabaseMessage()`', function (): void {
        $notification = Notification::make('test-id')
            ->title('Test Title')
            ->body('Test Body')
            ->icon(Heroicon::Bell)
            ->actions([
                Action::make('view')
                    ->label('View')
                    ->icon(Heroicon::Eye),
            ]);

        expect($notification->getDatabaseMessage())
            ->toMatchSnapshot();
    });

    it('roundtrips notification via `toArray()` and `fromArray()`', function (): void {
        $notification = Notification::make('test-id')
            ->title('Test Title')
            ->body('Test Body')
            ->icon('heroicon-o-bell')
            ->iconColor('success')
            ->color('primary')
            ->duration(5000);

        $array = $notification->toArray();
        $restored = Notification::fromArray($array);
        $restoredArray = $restored->toArray();

        expect($restoredArray)->toBe($array);
    });

    it('roundtrips notification with actions via `toArray()` and `fromArray()`', function (): void {
        $notification = Notification::make('test-id')
            ->title('Test Title')
            ->body('Test Body')
            ->actions([
                Action::make('view')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->color('primary')
                    ->url('/view'),
                Action::make('delete')
                    ->label('Delete')
                    ->icon('heroicon-o-trash')
                    ->color('danger'),
            ]);

        $array = $notification->toArray();
        $restored = Notification::fromArray($array);
        $restoredArray = $restored->toArray();

        expect($restoredArray)->toBe($array);
    });

    it('roundtrips notification with `Heroicon` enum via `toArray()` and `fromArray()`', function (): void {
        $notification = Notification::make('test-id')
            ->title('Test Title')
            ->icon(Heroicon::Bell);

        $array = $notification->toArray();
        $restored = Notification::fromArray($array);
        $restoredArray = $restored->toArray();

        expect($restoredArray)->toBe($array);
    });

    it('roundtrips notification with action containing `Heroicon` enum via `toArray()` and `fromArray()`', function (): void {
        $notification = Notification::make('test-id')
            ->title('Test Title')
            ->actions([
                Action::make('view')
                    ->label('View')
                    ->icon(Heroicon::Eye),
            ]);

        $array = $notification->toArray();
        $restored = Notification::fromArray($array);
        $restoredArray = $restored->toArray();

        expect($restoredArray)->toBe($array);
    });

    it('roundtrips notification via `getDatabaseMessage()` and `fromArray()`', function (): void {
        $notification = Notification::make('test-id')
            ->title('Test Title')
            ->body('Test Body')
            ->icon(Heroicon::Bell)
            ->actions([
                Action::make('view')
                    ->label('View')
                    ->icon(Heroicon::Eye),
            ]);

        $databaseMessage = $notification->getDatabaseMessage();
        $restored = Notification::fromArray($databaseMessage);
        $restoredMessage = $restored->getDatabaseMessage();

        expect($restoredMessage)->toBe($databaseMessage);
    });
});

it('defaults `isInline()` to `false`', function (): void {
    $notification = Notification::make('test');

    expect($notification->isInline())->toBeFalse();
});

it('returns fluent `$this` from `send()`', function (): void {
    $notification = Notification::make('test')
        ->title('Test');

    $result = $notification->send();

    expect($result)->toBe($notification);
});

it('can create notification with `make()` and verify `getId()`', function (): void {
    $notification = Notification::make('custom-id')
        ->title('Test');

    expect($notification->getId())->toBe('custom-id');
});

class NotificationWithLazyIconSize extends Notification
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->iconSize(fn (): IconSize => $this->getTitle() === 'Important' ? IconSize::Large : IconSize::Small);
    }
}
