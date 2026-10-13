<?php

use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

uses(TestCase::class);

beforeEach(function (): void {
    Artisan::call('filament:assets');

    $this->actingAs(User::factory()->create());
});

it('delivers notifications once across redirects', function (string $redirect): void {
    visit('/notification-browser-test')
        ->click("[data-testid=\"send-{$redirect}-redirect-notification\"]")
        ->assertQueryStringHas('redirected', '1')
        ->assertCount('.fi-no .fi-no-notification', 1)
        ->assertSeeIn('.fi-no .fi-no-notification', 'Redirect notification')
        ->assertNoSmoke()
        ->refresh()
        ->click('[data-testid="send-persistent-notification"]')
        ->assertCount('.fi-no .fi-no-notification', 1)
        ->assertSeeIn('.fi-no .fi-no-notification', 'Persistent notification')
        ->assertNoSmoke();
})->with(['full', 'spa']);

it('dispatches `notificationClosed` when inline notifications expire', function (): void {
    visit('/notification-browser-test')
        ->click('[data-testid="show-inline-notification"]')
        ->assertVisible('[data-testid="inline-notification"] .fi-inline')
        ->assertSeeIn('[data-testid="inline-notification-expiry"]', 'Expired');
});

it('restores focus only after explicit dismissal', function (): void {
    $notification = '.fi-no-notification';
    $closeButton = "{$notification} .fi-no-notification-close-btn";
    $origin = '[data-testid="send-persistent-notification"]';

    visit('/notification-browser-test')
        ->click($origin)
        ->assertVisible($notification)
        ->click($closeButton)
        ->assertMissing($notification)
        ->assertPresent($origin . ':focus')
        ->click($origin)
        ->assertVisible($notification)
        ->keys($closeButton, 'Escape')
        ->assertMissing($notification)
        ->assertPresent($origin . ':focus');
});

it('does not move focus on passive expiry or external closure', function (): void {
    $notification = '.fi-no-notification';
    $timedOrigin = '[data-testid="send-short-timed-notification"]';
    $persistentOrigin = '[data-testid="send-persistent-notification"]';
    $externalClose = '[data-testid="close-notification-externally"]';

    visit('/notification-browser-test')
        ->click($timedOrigin)
        ->assertVisible($notification)
        ->keys($timedOrigin, 'Tab')
        ->assertPresent($persistentOrigin . ':focus')
        ->assertMissing($notification)
        ->assertPresent($persistentOrigin . ':focus')
        ->click($persistentOrigin)
        ->assertVisible($notification)
        ->click($externalClose)
        ->assertMissing($notification)
        ->assertPresent($externalClose . ':focus');
});

it('closes an action group before dismissing its notification with `Escape`', function (): void {
    $notification = '.fi-no-notification';
    $groupedOrigin = '[data-testid="send-grouped-action-notification"]';
    $groupTrigger = '[data-testid="action-group"]';
    $groupedAction = '[data-testid="grouped-action"]';

    visit('/notification-browser-test')
        ->click($groupedOrigin)
        ->click($groupTrigger)
        ->assertVisible($groupedAction)
        ->keys($groupTrigger, 'ArrowDown')
        ->assertPresent($groupedAction . ':focus')
        ->keys($groupedAction, 'Escape')
        ->assertMissing($groupedAction)
        ->assertVisible($notification)
        ->assertPresent($groupTrigger . ':focus')
        ->keys($groupTrigger, 'Escape')
        ->assertMissing($notification)
        ->assertPresent($groupedOrigin . ':focus');
});

it('supports keyboard navigation in an action group menu', function (): void {
    $groupedOrigin = '[data-testid="send-grouped-action-notification"]';
    $groupTrigger = '[data-testid="action-group"]';
    $groupedAction = '[data-testid="grouped-action"]';
    $disabledAction = '[data-testid="disabled-action"]';
    $linkAction = '[data-testid="link-action"]';
    $postAction = '[data-testid="post-action"]';
    $nestedGroup = '[data-testid="nested-group"]';
    $nestedAction = '[data-testid="nested-action"]';

    visit('/notification-browser-test')
        ->click($groupedOrigin)
        ->assertAttribute($groupTrigger, 'aria-haspopup', 'menu')
        ->click($groupTrigger)
        ->assertPresent($groupTrigger . ':focus')
        ->keys($groupTrigger, 'ArrowDown')
        ->assertPresent($groupedAction . ':focus')
        ->keys($groupedAction, 'ArrowDown')
        ->assertPresent($disabledAction . ':focus')
        ->keys($disabledAction, 'Enter')
        ->assertVisible($groupedAction)
        ->assertPresent($disabledAction . ':focus')
        ->keys($disabledAction, 'ArrowUp')
        ->assertPresent($groupedAction . ':focus')
        ->keys($groupedAction, 'ArrowUp')
        ->assertPresent($nestedGroup . ':focus')
        ->assertMissing($nestedAction)
        ->keys($nestedGroup, 'ArrowDown')
        ->assertPresent($groupedAction . ':focus')
        ->keys($groupedAction, 'ArrowDown')
        ->assertPresent($disabledAction . ':focus')
        ->keys($disabledAction, 'ArrowDown')
        ->assertPresent($linkAction . ':focus')
        ->keys($linkAction, 'ArrowDown')
        ->assertPresent($postAction . ':focus')
        ->keys($postAction, 'ArrowDown')
        ->assertPresent($nestedGroup . ':focus')
        ->keys($nestedGroup, 'Enter')
        ->assertNotPresent('[role="menu"][aria-expanded]')
        ->assertPresent($nestedAction . ':focus')
        ->keys($nestedAction, 'ArrowLeft')
        ->assertPresent($nestedGroup . ':focus')
        ->assertAttribute($nestedGroup, 'aria-expanded', 'false')
        ->assertMissing($nestedAction)
        ->keys($nestedGroup, 'Space')
        ->assertPresent($nestedAction . ':focus')
        ->keys($nestedAction, 'ArrowLeft')
        ->keys($nestedGroup, 'ArrowUp')
        ->assertPresent($postAction . ':focus')
        ->keys($postAction, 'Escape')
        ->assertMissing($groupedAction)
        ->assertPresent($groupTrigger . ':focus')
        ->click($groupTrigger)
        ->assertPresent($groupTrigger . ':focus')
        ->assertAttribute($groupTrigger, 'aria-expanded', 'true')
        ->click($groupTrigger)
        ->assertAttribute($groupTrigger, 'aria-expanded', 'false')
        ->keys($groupTrigger, 'ArrowUp')
        ->assertPresent($nestedGroup . ':focus')
        ->keys($nestedGroup, 'Tab')
        ->assertMissing($groupedAction)
        ->assertNotPresent($groupTrigger . ':focus, ' . $nestedGroup . ':focus');
});

it('preserves keyboard activation of explicitly styled nested action group triggers', function (): void {
    $trigger = '[data-testid="styled-nested-trigger"]';
    $item = '[data-testid="styled-nested-action"]';

    visit('/notification-browser-test')
        ->keys('[data-testid="responsive-action-group"]:visible', 'Enter')
        ->assertVisible($trigger)
        ->assertAttributeMissing($trigger, 'role')
        ->keys($trigger, 'Enter')
        ->assertPresent($item . ':focus')
        ->keys($item, 'Escape')
        ->assertMissing($item)
        ->assertPresent($trigger . ':focus')
        ->keys($trigger, ' ')
        ->assertPresent($item . ':focus')
        ->assertNoSmoke();
});

it('synchronizes a responsive action group trigger across breakpoints', function (): void {
    $trigger = '[data-testid="responsive-action-group"]:visible';
    $item = '[data-testid="responsive-action"]';

    visit('/notification-browser-test')
        ->resize(900, 812)
        ->assertAttribute($trigger, 'aria-haspopup', 'menu')
        ->resize(375, 812)
        ->assertAttribute($trigger, 'aria-haspopup', 'menu')
        ->assertScript("(() => { const trigger = Array.from(document.querySelectorAll('[data-testid=\"responsive-action-group\"]')).find((trigger) => trigger.checkVisibility()); return document.getElementById(trigger.getAttribute('aria-controls')).contains(document.querySelector('[data-testid=\"responsive-action\"]')) })()", true)
        ->assertScript("(() => { const trigger = Array.from(document.querySelectorAll('[data-testid=\"responsive-action-group\"]')).find((trigger) => trigger.checkVisibility()); return trigger.id === document.getElementById(trigger.getAttribute('aria-controls')).getAttribute('aria-labelledby') })()", true)
        ->assertScript("(() => { const ids = Array.from(document.querySelectorAll('[data-testid=\"responsive-action-group\"]')).map((trigger) => trigger.id).filter(Boolean); return ids.length === new Set(ids).size })()", true)
        ->keys($trigger, 'Enter')
        ->assertPresent($item . ':focus')
        ->resize(900, 812)
        ->assertMissing($item)
        ->assertAttribute($trigger, 'aria-expanded', 'false')
        ->assertScript("(() => { const trigger = Array.from(document.querySelectorAll('[data-testid=\"responsive-action-group\"]')).find((trigger) => trigger.checkVisibility()); return trigger.id === document.getElementById(trigger.getAttribute('aria-controls')).getAttribute('aria-labelledby') })()", true)
        ->assertNoSmoke();
});

it('has no accessibility issues in an open action group menu in light and dark modes', function (): void {
    $page = visit('/notification-browser-test');

    foreach ([$page, $page->inDarkMode()] as $themedPage) {
        $themedPage
            ->click('[data-testid="send-grouped-action-notification"]')
            ->assertVisible('[data-testid="action-group"]')
            ->keys('[data-testid="action-group"]', 'Enter')
            ->assertVisible('[data-testid="grouped-action"]')
            ->keys('[data-testid="grouped-action"]', 'ArrowDown')
            ->assertPresent('[data-testid="disabled-action"]:focus')
            ->assertNoSmoke()
            ->assertNoAccessibilityIssues();
    }
});

it('removes notifications when navigating away', function (): void {
    visit('/notification-browser-test')
        ->click('[data-testid="send-short-timed-notification"]')
        ->assertVisible('.fi-no-notification')
        ->click('[data-testid="navigate-away"]')
        ->assertPathIs('/')
        ->assertMissing('.fi-no-notification');
});

it('has no accessibility issues in light and dark modes', function (): void {
    $page = visit('/notification-browser-test');

    foreach ([$page, $page->inDarkMode()] as $themedPage) {
        $themedPage
            ->click('[data-testid="send-persistent-notification"]')
            ->assertVisible('.fi-no-notification')
            ->assertNoSmoke()
            ->assertNoAccessibilityIssues();
    }
});
