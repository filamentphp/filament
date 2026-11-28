<?php

use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Panel;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Fixtures\Pages\ErrorNotificationsBrowserTest;
use Filament\Tests\TestCase;
use Illuminate\Foundation\Http\Middleware\TrimStrings;
use Illuminate\Support\Facades\Artisan;

uses(TestCase::class);

describe('configuration', function (): void {
    it('enables error notifications by default and can disable and re-enable them', function (): void {
        $panel = Panel::make();

        expect($panel->hasErrorNotifications())->toBeTrue()
            ->and($panel->errorNotifications(static fn (): bool => false)->hasErrorNotifications())->toBeFalse()
            ->and($panel->errorNotifications()->hasErrorNotifications())->toBeTrue();
    });

    it('provides a generic notification and leaves HTTP `419` to Livewire by default', function (): void {
        $notifications = Panel::make()->getErrorNotifications();

        expect($notifications['']['title'])->toBeString()->not->toBeEmpty()
            ->and($notifications['']['body'])->toBeString()->not->toBeEmpty()
            ->and($notifications['']['isHidden'])->toBeFalse()
            ->and($notifications['']['isDisabled'])->toBeFalse()
            ->and($notifications[419]['isDisabled'])->toBeTrue();
    });

    it('evaluates custom notification text and overrides disabled registrations', function (): void {
        $panel = Panel::make()
            ->hiddenErrorNotification(403)
            ->disabledErrorNotification(503)
            ->registerErrorNotification(
                title: static fn (): string => 'Try again',
                body: static fn (): string => 'The service is unavailable.',
                statusCode: 503,
            );

        expect($panel->getErrorNotifications()[403]['isHidden'])->toBeTrue()
            ->and($panel->getErrorNotifications()[503])->toBe([
                'title' => 'Try again',
                'body' => 'The service is unavailable.',
                'isHidden' => false,
                'isDisabled' => false,
            ]);
    });
});

describe('browser', function (): void {
    beforeEach(function (): void {
        Artisan::call('filament:assets');

        $this->actingAs(User::factory()->create());
    });

    it('shows an error for a failed action and allows subsequent successful actions', function (): void {
        visit(ErrorNotificationsBrowserTest::getUrl(panel: 'spa'))
            ->click('[data-testid="save"]')
            ->assertSeeIn('[data-testid="saved-count"]', '1')
            ->assertMissing('[role="alert"]')
            ->click('[data-testid="fail-500"]')
            ->assertCount('[role="alert"]', 1)
            ->assertMissing('dialog[open]')
            ->click('[data-testid="save"]')
            ->assertSeeIn('[data-testid="saved-count"]', '2')
            ->assertSee('Changes saved');
    });

    it('uses status-specific text before the generic fallback', function (): void {
        Filament::getPanel('spa')
            ->registerErrorNotification('Request failed', 'Please retry your changes.')
            ->registerErrorNotification('Record not found', 'This record is no longer available.', statusCode: 404);

        visit(ErrorNotificationsBrowserTest::getUrl(panel: 'spa'))
            ->click('[data-testid="fail-500"]')
            ->assertSeeIn('[role="alert"]', 'Request failed')
            ->assertSeeIn('[role="alert"]', 'Please retry your changes.')
            ->click('[data-testid="fail-404"]')
            ->assertSee('Record not found')
            ->assertSee('This record is no longer available.')
            ->assertSeeIn('[data-testid="error-notification-count"]', '2')
            ->assertMissing('dialog[open]');
    });

    it('hides configured errors without falling back to Livewire error handling', function (int $status): void {
        Filament::getPanel('spa')->hiddenErrorNotification($status);

        visit(ErrorNotificationsBrowserTest::getUrl(panel: 'spa'))
            ->click('[data-testid="fail-' . $status . '"]')
            ->click('[data-testid="save"]')
            ->assertSeeIn('[data-testid="saved-count"]', '1')
            ->assertSeeIn('[data-testid="error-notification-count"]', '0')
            ->assertMissing('[role="alert"]')
            ->assertMissing('dialog[open]');
    })->with([403, 419]);

    it('preserves Livewire error handling when notifications are disabled', function (bool $entirePanel): void {
        $panel = Filament::getPanel('spa');

        if ($entirePanel) {
            $panel->errorNotifications(false);
        } else {
            $panel->disabledErrorNotification(503);
        }

        visit(ErrorNotificationsBrowserTest::getUrl(panel: 'spa'))
            ->click('[data-testid="fail-503"]')
            ->assertVisible('dialog[open]')
            ->assertMissing('[role="alert"]');
    })->with(['panel' => true, 'status' => false]);

    it('does not report an HTTP error from the notification request as another notification', function (): void {
        app()->instance(TrimStrings::class, new class extends TrimStrings
        {
            public int $failedRequests = 0;

            public function handle($request, Closure $next)
            {
                if ($request->hasHeader('X-Livewire') && (++$this->failedRequests <= 2)) {
                    return response('Service unavailable', 503);
                }

                return parent::handle($request, $next);
            }
        });

        visit(ErrorNotificationsBrowserTest::getUrl(panel: 'spa'))
            ->click('[data-testid="fail-500"]')
            ->assertVisible('dialog[open]')
            ->assertMissing('[role="alert"]')
            ->keys('dialog[open]', 'Escape')
            ->click('[data-testid="save"]')
            ->assertSeeIn('[data-testid="saved-count"]', '1')
            ->assertSee('Changes saved')
            ->assertSeeIn('[data-testid="error-notification-count"]', '1')
            ->assertMissing('[role="alert"]');
    });

    it('reports network failures without recursively reporting failed notification delivery', function (int $failedRequestCount): void {
        $page = visit(ErrorNotificationsBrowserTest::getUrl(panel: 'spa'));

        // Pest Browser has no network-failure API. Reject the action request and optionally
        // its notification request, then restore the real network for subsequent delivery.
        $page->script(<<<JS
            () => {
                const originalFetch = window.fetch
                let failedRequests = 0

                window.fetch = (resource, options) => {
                    if (!new Headers(options?.headers).has('X-Livewire')) {
                        return originalFetch(resource, options)
                    }

                    document.querySelector('[data-testid="network-failures"]').textContent = ++failedRequests

                    if (failedRequests === {$failedRequestCount}) {
                        window.fetch = originalFetch
                    }

                    return Promise.reject(new TypeError('Failed to fetch'))
                }
            }
            JS);

        $page->click('[data-testid="fail-500"]')
            ->assertSeeIn('[data-testid="network-failures"]', (string) $failedRequestCount)
            ->click('[data-testid="save"]')
            ->assertSeeIn('[data-testid="saved-count"]', '1')
            ->assertSee('Changes saved')
            ->assertSeeIn('[data-testid="error-notification-count"]', '1');
    })->with(['action only' => 1, 'action and notification' => 2]);

    it('renders accessible error notifications in light and dark modes', function (): void {
        Notification::configureUsing(static fn (Notification $notification): Notification => $notification->persistent());

        $page = visit(ErrorNotificationsBrowserTest::getUrl(panel: 'spa'));

        foreach ([$page, $page->inDarkMode()] as $themedPage) {
            $themedPage
                ->click('[data-testid="fail-500"]')
                ->assertCount('[role="alert"]', 1)
                ->assertNoAccessibilityIssues();
        }
    });
});
