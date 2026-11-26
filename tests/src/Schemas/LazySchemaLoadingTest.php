<?php

namespace Filament\Tests\Schemas;

use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

uses(TestCase::class);

beforeEach(function (): void {
    Artisan::call('filament:assets');
});

it('loads lazy-loaded sections, tabs, and wizard steps as they are revealed', function (): void {
    $this->actingAs(User::factory()->create());

    $page = visit('/lazy-schema-loading-browser-test', ['reducedMotion' => 'reduce']);

    $page->assertPresent('[data-testid="viewport-lazy-loaded-section"] [role="status"][aria-busy="true"]')
        ->assertNotPresent('#form\.lazyLoadedDetails\.lazy_loaded_name')
        ->assertNotPresent('#form\.lazyLoadedTabs\.profileTab\.profile_name')
        ->assertNotPresent('#form\.lazyLoadedTabs\.preferencesTab\.timezone')
        ->assertNotPresent('#form\.lazyLoadedWizard\.accountStep\.account_name')
        ->assertNotPresent('#form\.lazyLoadedWizard\.confirmationStep\.confirmation_note')
        ->assertPresent('[data-testid="concealed-lazy-loaded-section"] [aria-busy="true"]')
        ->assertNotPresent('#form\.concealedLazyLoadedDetails\.concealed_lazy_loaded_name')
        ->assertNoAccessibilityIssues();

    $page->page()->locator('[data-testid="concealed-lazy-loaded-section"] [aria-expanded]')->click();
    $page->assertVisible('#form\.concealedLazyLoadedDetails\.concealed_lazy_loaded_name')
        ->assertMissing('[data-testid="concealed-lazy-loaded-section"] [aria-busy="true"]');

    $page->page()->locator('[data-testid="viewport-lazy-loaded-section"]')->scrollIntoViewIfNeeded();
    $page->assertVisible('#form\.lazyLoadedDetails\.lazy_loaded_name')
        ->assertMissing('[data-testid="viewport-lazy-loaded-section"] [aria-busy="true"]');

    $page->page()->locator('[data-testid="lazy-loaded-tabs"]')->scrollIntoViewIfNeeded();
    $page->assertVisible('#form\.lazyLoadedTabs\.profileTab\.profile_name')
        ->assertNotPresent('#form\.lazyLoadedTabs\.preferencesTab\.timezone')
        ->click('[data-testid="lazy-loaded-tabs"] [role="tab"] >> text=Preferences')
        ->assertVisible('#form\.lazyLoadedTabs\.preferencesTab\.timezone');

    $page->page()->locator('[data-testid="lazy-loaded-wizard"]')->scrollIntoViewIfNeeded();
    $page->assertVisible('#form\.lazyLoadedWizard\.accountStep\.account_name')
        ->assertNotPresent('#form\.lazyLoadedWizard\.confirmationStep\.confirmation_note')
        ->click('Next')
        ->assertVisible('#form\.lazyLoadedWizard\.confirmationStep\.confirmation_note')
        ->assertNoSmoke()
        ->assertNoAccessibilityIssues();

    $darkPage = $page->inDarkMode();
    $darkPage->assertPresent('[data-testid="viewport-lazy-loaded-section"] [aria-busy="true"]')
        ->assertNoAccessibilityIssues();
    $darkPage->page()->locator('[data-testid="concealed-lazy-loaded-section"] [aria-expanded]')->click();
    $darkPage->assertVisible('#form\.concealedLazyLoadedDetails\.concealed_lazy_loaded_name');
    $darkPage->page()->locator('[data-testid="viewport-lazy-loaded-section"]')->scrollIntoViewIfNeeded();
    $darkPage->assertVisible('#form\.lazyLoadedDetails\.lazy_loaded_name');
    $darkPage->page()->locator('[data-testid="lazy-loaded-tabs"]')->scrollIntoViewIfNeeded();
    $darkPage->click('[data-testid="lazy-loaded-tabs"] [role="tab"] >> text=Preferences')
        ->assertVisible('#form\.lazyLoadedTabs\.preferencesTab\.timezone');
    $darkPage->page()->locator('[data-testid="lazy-loaded-wizard"]')->scrollIntoViewIfNeeded();
    $darkPage->assertVisible('#form\.lazyLoadedWizard\.accountStep\.account_name')
        ->click('Next')
        ->assertVisible('#form\.lazyLoadedWizard\.confirmationStep\.confirmation_note')
        ->assertNoAccessibilityIssues();
});

it('replaces custom loading content with the loaded schema', function (): void {
    $this->actingAs(User::factory()->create());

    $page = visit('/lazy-schema-loading-browser-test?hasCustomLoadingPlaceholder=1');

    $page->assertPresent('[data-testid="custom-loading-placeholder"]')
        ->assertNotPresent('#form\.concealedLazyLoadedDetails\.concealed_lazy_loaded_name')
        ->click('[data-testid="concealed-lazy-loaded-section"] [aria-expanded]')
        ->assertVisible('#form\.concealedLazyLoadedDetails\.concealed_lazy_loaded_name')
        ->assertNotPresent('[data-testid="custom-loading-placeholder"]')
        ->assertNoSmoke()
        ->assertNoAccessibilityIssues();
});

it('prefetches an inactive tab in an action modal after rendering', function (): void {
    $user = User::factory()->create(['email' => 'team@example.com']);
    $this->actingAs($user);

    $page = visit('/lazy-schema-loading-browser-test', ['reducedMotion' => 'reduce']);
    $modal = '[data-testid="prefetch-modal"]';
    $profile = $modal . ' [role="tab"][data-tab-key="profile"]';
    $notifications = $modal . ' [role="tab"][data-tab-key="notifications"]';
    $placeholder = $modal . ' [data-testid="custom-loading-placeholder"]';
    $name = $modal . ' [data-testid="prefetch-name"]';
    $email = $modal . ' [data-testid="prefetch-email"]';

    // Hold the response for the notifications schema while switching tabs.
    $page->page()->evaluate(<<<'JS'
        ([profile, name]) => {
            const fetch = window.fetch
            const prefetch = window.schemaPrefetch = { requests: 0 }
            const release = new Promise(resolve => { prefetch.release = resolve })

            window.fetch = async (url, options) => {
                if (!new Headers(options?.headers).has('X-Livewire')) {
                    return fetch(url, options)
                }

                const isNotificationsLoad = JSON.parse(options.body).components.some(({ calls }) =>
                    calls.some(({ method, params }) =>
                        method === 'loadLazySchema' && params[0].endsWith('.settings.notifications'),
                    ),
                )

                if (!isNotificationsLoad || ++prefetch.requests !== 1) {
                    return fetch(url, options)
                }

                prefetch.profileWasVisible = document.querySelector(profile)?.getAttribute('aria-selected') === 'true' &&
                    document.querySelector(name)?.checkVisibility()

                const response = await fetch(url, options)
                await release

                return response
            }
        }
        JS, [$profile, $name]);

    $page->click('[data-testid="open-prefetch-modal"]')->assertVisible($name);
    $page->page()->waitForFunction('() => window.schemaPrefetch.requests > 0');
    $page->assertScript('window.schemaPrefetch.profileWasVisible', true)
        ->fill($name, 'Ada')
        ->click($notifications)
        ->assertVisible($placeholder)
        ->click($profile);

    $page->page()->evaluate('window.schemaPrefetch.release()');
    $page->page()->locator($email)->waitFor(['state' => 'attached']);

    $page->assertAttribute($profile, 'aria-selected', 'true')
        ->assertValue($name, 'Ada')
        ->click($notifications)
        ->assertVisible($email)
        ->assertValue($email, 'team@example.com')
        ->assertNotPresent($placeholder)
        ->assertNoAccessibilityIssues()
        ->fill($email, 'invalid')
        ->click($profile)
        ->click('Save settings')
        ->assertVisible($modal . ' [data-validation-error]')
        ->fill($email, 'ada@example.com')
        ->click('Save settings');

    $page->page()->locator($modal)->waitFor(['state' => 'hidden']);
    $page->assertScript('window.schemaPrefetch.requests', 1)->assertNoSmoke();

    expect($user->refresh())
        ->name->toBe('Ada')
        ->email->toBe('ada@example.com');
});

it('shows an accessible inline error when a lazy-loaded schema fails', function (bool $hasCustomLoadingPlaceholder): void {
    $this->actingAs(User::factory()->create());

    $page = visit('/lazy-schema-loading-browser-test?shouldFailLoading=1&hasCustomLoadingPlaceholder=' . (int) $hasCustomLoadingPlaceholder);

    $page->page()->locator('[data-testid="concealed-lazy-loaded-section"] [aria-expanded]')->click();
    $page->assertVisible('[data-testid="concealed-lazy-loaded-section"] [role="alert"][aria-busy="false"]')
        ->assertNotPresent('#form\\.concealedLazyLoadedDetails\\.concealed_lazy_loaded_name')
        ->assertMissing('[data-testid="custom-loading-placeholder"]')
        ->assertMissing('dialog[open]')
        ->assertNoAccessibilityIssues();

    $darkPage = $page->inDarkMode();
    $darkPage->page()->locator('[data-testid="concealed-lazy-loaded-section"] [aria-expanded]')->click();
    $darkPage->assertVisible('[data-testid="concealed-lazy-loaded-section"] [role="alert"][aria-busy="false"]')
        ->assertMissing('[data-testid="custom-loading-placeholder"]')
        ->assertNoAccessibilityIssues();
})->with([false, true]);

it('loads a lazy-loaded schema when it contains a validation error', function (): void {
    $this->actingAs(User::factory()->create());

    visit('/lazy-schema-loading-browser-test')
        ->assertNotPresent('#form\.lazyLoadedDetails\.lazy_loaded_name')
        ->click('Validate')
        ->assertVisible('#form\.lazyLoadedDetails\.lazy_loaded_name')
        ->assertVisible('[data-testid="viewport-lazy-loaded-section"] [data-validation-error]')
        ->assertMissing('[data-testid="viewport-lazy-loaded-section"] [aria-busy="true"]')
        ->assertNoSmoke();
});
