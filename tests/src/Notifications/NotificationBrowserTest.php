<?php

use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

uses(TestCase::class);

beforeEach(function (): void {
    Artisan::call('filament:assets');

    $this->actingAs(User::factory()->create());
});

it('pauses the remaining lifetime while hovered or focused', function (): void {
    $notification = '.fi-no-notification';
    $closeButton = "{$notification} .fi-no-notification-close-btn";
    $origin = '[data-testid="send-timed-notification"]';

    $page = visit('/notification-browser-test')
        ->click($origin)
        ->assertVisible($notification)
        ->wait(0.9)
        ->hover($notification)
        ->wait(1.4)
        ->assertVisible($notification)
        ->assertScript("(document.querySelector('{$closeButton}').focus(), document.activeElement.matches('{$closeButton}'))", true)
        ->hover($origin)
        ->wait(1.1)
        ->assertVisible($notification)
        ->assertScript("(document.querySelector('{$origin}').focus(), document.activeElement.matches('{$origin}'))", true)
        ->wait(0.35)
        ->assertVisible($notification)
        ->wait(0.8);

    expect($page->script("document.querySelector('{$notification}') === null"))->toBeTrue();
});

it('keeps finite duration behavior for inline notifications', function (): void {
    visit('/notification-browser-test')
        ->assertScript("(sessionStorage.setItem('inlineNotificationClosedCount', '0'), window.addEventListener('notificationClosed', (event) => event.detail.id === 'inline-timed-notification' && sessionStorage.setItem('inlineNotificationClosedCount', String(Number(sessionStorage.getItem('inlineNotificationClosedCount')) + 1))), true)")
        ->click('[data-testid="show-inline-notification"]')
        ->assertVisible('[data-testid="inline-notification"] .fi-inline')
        ->wait(0.7)
        ->assertScript("sessionStorage.getItem('inlineNotificationClosedCount')", '1');
});

it('restores focus only after explicit dismissal', function (): void {
    $notification = '.fi-no-notification';
    $closeButton = "{$notification} .fi-no-notification-close-btn";
    $origin = '[data-testid="send-persistent-notification"]';
    $newOrigin = '[data-testid="close-notification-externally"]';

    visit('/notification-browser-test')
        ->click($origin)
        ->assertVisible($notification)
        ->assertScript("(document.querySelector('{$closeButton}').focus(), document.activeElement.matches('{$closeButton}'))", true)
        ->click($closeButton)
        ->wait(0.5)
        ->assertMissing($notification)
        ->assertScript("document.activeElement.matches('{$origin}')", true)
        ->click($origin)
        ->assertVisible($notification)
        ->assertScript("(document.querySelector('{$closeButton}').focus(), document.activeElement.matches('{$closeButton}'))", true)
        ->assertScript("(document.querySelector('{$newOrigin}').focus(), document.activeElement.matches('{$newOrigin}'))", true)
        ->assertScript("(document.querySelector('{$closeButton}').focus(), document.activeElement.matches('{$closeButton}'))", true)
        ->keys($closeButton, 'Escape')
        ->wait(0.5)
        ->assertMissing($notification)
        ->assertScript("document.activeElement.matches('{$newOrigin}')", true);
});

it('does not move focus on passive expiry or external closure', function (): void {
    $notification = '.fi-no-notification';
    $closeButton = "{$notification} .fi-no-notification-close-btn";
    $timedOrigin = '[data-testid="send-short-timed-notification"]';
    $persistentOrigin = '[data-testid="send-persistent-notification"]';

    $page = visit('/notification-browser-test')
        ->click($timedOrigin)
        ->assertVisible($notification)
        ->assertScript("(document.querySelector('{$persistentOrigin}').focus(), document.activeElement.matches('{$persistentOrigin}'))", true)
        ->wait(1.3)
        ->assertMissing($notification)
        ->assertScript("document.activeElement.matches('{$persistentOrigin}')", true)
        ->click($persistentOrigin)
        ->assertVisible($notification)
        ->assertScript("(document.querySelector('{$closeButton}').focus(), document.activeElement.matches('{$closeButton}'))", true)
        ->assertScript("window.dispatchEvent(new CustomEvent('close-notification', { detail: { id: 'persistent-notification' } }))")
        ->wait(0.5)
        ->assertMissing($notification);

    $page->assertScript("!document.activeElement.matches('{$persistentOrigin}')", true);
});

it('does not guess a focus target when the origin has been removed', function (): void {
    $notification = '.fi-no-notification';
    $closeButton = "{$notification} .fi-no-notification-close-btn";
    $origin = '[data-testid="send-persistent-notification"]';

    visit('/notification-browser-test')
        ->click($origin)
        ->assertVisible($notification)
        ->assertScript("(document.querySelector('{$closeButton}').focus(), document.activeElement.matches('{$closeButton}'))", true)
        ->assertScript("(document.querySelector('{$origin}').remove(), true)")
        ->assertScript("(document.querySelector('{$closeButton}').click(), true)")
        ->wait(0.5)
        ->assertMissing($notification)
        ->assertScript('document.activeElement === document.body', true);
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
        ->assertScript("(document.querySelector('{$groupedAction}').focus(), document.activeElement.matches('{$groupedAction}'))", true)
        ->keys($groupedAction, 'Escape')
        ->assertMissing($groupedAction)
        ->assertVisible($notification)
        ->assertScript("document.activeElement.matches('{$groupTrigger}')", true)
        ->keys($groupTrigger, 'Escape')
        ->wait(0.5)
        ->assertMissing($notification)
        ->assertScript("document.activeElement.matches('{$groupedOrigin}')", true);
});

it('cleans up an expiring notification when navigating away', function (): void {
    visit('/notification-browser-test')
        ->assertScript("(sessionStorage.setItem('notificationClosedCount', '0'), window.addEventListener('notificationClosed', () => sessionStorage.setItem('notificationClosedCount', String(Number(sessionStorage.getItem('notificationClosedCount')) + 1))), true)")
        ->click('[data-testid="send-short-timed-notification"]')
        ->assertVisible('.fi-no-notification')
        ->click('[data-testid="navigate-away"]')
        ->wait(1.3)
        ->assertScript("sessionStorage.getItem('notificationClosedCount')", '0');
});

it('has no accessibility issues in light and dark modes', function (bool $isDarkMode): void {
    $page = visit('/notification-browser-test');

    if ($isDarkMode) {
        $page->inDarkMode();
    }

    $page
        ->click('[data-testid="send-persistent-notification"]')
        ->assertVisible('.fi-no-notification')
        ->assertNoSmoke()
        ->assertNoAccessibilityIssues();
})->with(['light' => false, 'dark' => true]);
