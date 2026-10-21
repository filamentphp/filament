<?php

use Filament\Notifications\Livewire\Notifications;
use Filament\Notifications\Notification;
use Filament\Notifications\NotificationMailbox;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Fixtures\Pages\NotificationBrowserTest;
use Filament\Tests\TestCase;
use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\Repository;
use Illuminate\Contracts\Cache\Store as CacheStore;
use Illuminate\Foundation\Http\Middleware\TrimStrings;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;

use function Filament\Tests\livewire;

uses(TestCase::class);

describe('delivery', function (): void {
    it('does not replay a consumed notification after a stale session is restored', function (): void {
        Route::middleware('web')->get('/notification-mailbox-send', static function () {
            Notification::make('mailbox-regression')->title('Saved')->send();

            return redirect('/');
        });

        $this->get('/notification-mailbox-send')->assertRedirect('/');
        $staleSession = session()->all();

        livewire(Notifications::class)
            ->dispatch('notificationsSent')
            ->assertCount('notifications', 1)
            ->assertSee('Saved');

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

        // Older Laravel versions bind `Cache::extend()` callbacks to the cache manager.
        Cache::extend('without-locks', fn (): Repository => Cache::repository($store));
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
        ], ['X-Livewire' => 'true'])
            ->assertOk()
            ->assertJsonPath('components.0.effects.redirect', NotificationBrowserTest::getUrl(['redirected' => 1]));

        livewire(Notifications::class)->dispatch('notificationsSent')->assertCount('notifications', 0);

        livewire(Notifications::class)->call('pullNotificationsFromSession', [$response->headers->get('X-Filament-Notifications')])
            ->assertCount('notifications', 1)
            ->assertSee('Redirect notification');

        livewire(Notifications::class)->dispatch('notificationsSent')->assertCount('notifications', 0);
    });

    it('delivers a Livewire batch only to its response owner while retaining unowned catch-up', function (string $method, array $parameters, string $title): void {
        $this->actingAs(User::factory()->create());
        $producer = livewire(NotificationBrowserTest::class);

        $response = $this->postJson(Livewire::getUpdateUri(), [
            'components' => [[
                'snapshot' => json_encode($producer->snapshot),
                'updates' => [],
                'calls' => [['method' => $method, 'params' => $parameters]],
            ]],
        ], ['X-Livewire' => 'true'])->assertOk()->assertJsonPath('filamentNotifications', true);

        $batch = $response->headers->get('X-Filament-Notifications');

        $this->postJson(Livewire::getUpdateUri(), [
            'components' => [[
                'snapshot' => json_encode($producer->snapshot),
                'updates' => [],
                'calls' => [],
            ]],
        ], ['X-Livewire' => 'true'])->assertOk()->assertJsonMissingPath('filamentNotifications');

        livewire(Notifications::class)->call('pullNotificationsFromSession')->assertCount('notifications', 0);
        expect($batch)->toMatch('/\A[a-f0-9]{40}\z/');

        Route::middleware('web')->get('/notification-unowned-catch-up', static function () {
            Notification::make()->title('Unowned catch-up')->send();

            return response()->noContent();
        });
        $this->get('/notification-unowned-catch-up')->assertNoContent();

        livewire(Notifications::class)->call('pullNotificationsFromSession')
            ->assertCount('notifications', 1)->assertSee('Unowned catch-up')->assertDontSee($title);

        livewire(Notifications::class)->call('pullNotificationsFromSession', [$batch])
            ->assertCount('notifications', 1)->assertSee($title)->assertDontSee('Unowned catch-up');

        livewire(Notifications::class)->call('pullNotificationsFromSession', [$batch])->assertCount('notifications', 0);
    })->with([
        'action' => ['sendPersistentNotification', [], 'Persistent notification'],
        'full redirect' => ['sendRedirectNotification', [false], 'Redirect notification'],
        'SPA redirect' => ['sendRedirectNotification', [true], 'Redirect notification'],
    ]);
});

describe('session and authentication changes', function (): void {
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
            Notification::make('owned-before-invalidation')->title('Owned before invalidation')->send();
            app(NotificationMailbox::class)->flush(str_repeat('a', 40));

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
            ->assertSee('After authentication change')
            ->assertDontSee('Owned before invalidation');

        if ($shouldInvalidate) {
            $notifications
                ->assertDontSee('Published before invalidation')
                ->assertDontSee('Pending before invalidation');
        } else {
            $notifications
                ->assertSee('Published before invalidation')
                ->assertSee('Pending before invalidation');
        }

        $ownedNotifications = livewire(Notifications::class)->call('pullNotificationsFromSession', [str_repeat('a', 40)])
            ->assertCount('notifications', $shouldInvalidate ? 0 : 1);

        if (! $shouldInvalidate) {
            $ownedNotifications->assertSee('Owned before invalidation');
        }

        livewire(Notifications::class)->dispatch('notificationsSent')->assertCount('notifications', 0);
    })->with([
        'login' => [true, false],
        'logout' => [false, false],
        'login after invalidation' => [true, true],
        'logout after invalidation' => [false, true],
    ]);

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
});

describe('response middleware', function (): void {
    it('delivers notifications sent by global middleware after handling the request without changing Livewire return types', function (): void {
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

        livewire(Notifications::class)->call('pullNotificationsFromSession', [$response->headers->get('X-Filament-Notifications')])
            ->assertCount('notifications', 1)
            ->assertSee('Sent by global middleware');
    });
});
