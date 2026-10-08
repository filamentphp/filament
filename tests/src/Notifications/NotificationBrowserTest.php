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
        ->assertScript("document.activeElement.matches('{$groupTrigger}')", true)
        ->keys($groupTrigger, 'ArrowDown')
        ->assertScript("document.activeElement.matches('{$groupedAction}')", true)
        ->keys($groupedAction, 'ArrowDown')
        ->assertScript("document.activeElement.matches('{$disabledAction}')", true)
        ->keys($disabledAction, 'Enter')
        ->assertVisible($groupedAction)
        ->assertScript("document.activeElement.matches('{$disabledAction}')", true)
        ->keys($disabledAction, 'ArrowUp')
        ->assertScript("document.activeElement.matches('{$groupedAction}')", true)
        ->keys($groupedAction, 'ArrowUp')
        ->assertScript("document.activeElement.matches('{$nestedGroup}')", true)
        ->assertMissing($nestedAction)
        ->keys($nestedGroup, 'ArrowDown')
        ->assertScript("document.activeElement.matches('{$groupedAction}')", true)
        ->keys($groupedAction, 'ArrowDown')
        ->assertScript("document.activeElement.matches('{$disabledAction}')", true)
        ->keys($disabledAction, 'ArrowDown')
        ->assertScript("document.activeElement.matches('{$linkAction}')", true)
        ->keys($linkAction, 'ArrowDown')
        ->assertScript("document.activeElement.matches('{$postAction}')", true)
        ->keys($postAction, 'ArrowDown')
        ->assertScript("document.activeElement.matches('{$nestedGroup}')", true)
        ->assertScript("(document.querySelector('{$nestedGroup}').setAttribute('aria-disabled', 'true'), true)", true)
        ->keys($nestedGroup, 'ArrowRight')
        ->assertMissing($nestedAction)
        ->assertScript("(document.querySelector('{$nestedGroup}').removeAttribute('aria-disabled'), true)", true)
        ->keys($nestedGroup, 'Enter')
        ->assertScript("!document.querySelector('{$nestedGroup}').closest('[role=menu]').hasAttribute('aria-expanded')", true)
        ->assertScript("document.activeElement.matches('{$nestedAction}')", true)
        ->keys($nestedAction, 'ArrowLeft')
        ->assertScript("document.activeElement.matches('{$nestedGroup}')", true)
        ->keys($nestedGroup, ' ')
        ->assertScript("document.activeElement.matches('{$nestedAction}')", true)
        ->assertScript("(document.querySelector('{$nestedGroup}').closest('.fi-dropdown').remove(), true)")
        ->assertScript("document.activeElement.matches('{$postAction}')", true)
        ->keys($postAction, 'Escape')
        ->assertMissing($groupedAction)
        ->assertScript("document.activeElement.matches('{$groupTrigger}')", true)
        ->click($groupTrigger)
        ->assertScript("document.activeElement.matches('{$groupTrigger}')", true)
        ->assertScript(<<<'JS'
            (() => {
                const action = document.querySelector('[data-testid="post-action"]')
                action.replaceWith(action.cloneNode(true))

                return true
            })()
            JS, true)
        ->assertScript("document.activeElement.matches('{$groupTrigger}')", true)
        ->click($groupTrigger)
        ->keys($groupTrigger, 'ArrowUp')
        ->assertScript("document.activeElement.matches('{$postAction}')", true)
        ->keys($postAction, 'Tab')
        ->assertMissing($groupedAction)
        ->assertScript("!document.activeElement.matches('{$groupTrigger}, {$postAction}')", true)
        ->keys($groupTrigger, 'Enter')
        ->keys($groupedAction, 'ArrowDown')
        ->keys($disabledAction, 'ArrowDown')
        ->assertScript("document.activeElement.matches('{$linkAction}')", true)
        ->assertScript(<<<'JS'
            (() => {
                const link = document.querySelector('[data-testid="link-action"]')
                link.dispatchEvent(new KeyboardEvent('keydown', { key: ' ', bubbles: true }))
                setTimeout(() => document.activeElement.dispatchEvent(new KeyboardEvent('keyup', { key: ' ', bubbles: true })), 200)

                return true
            })()
            JS, true)
        ->wait(0.4)
        ->assertMissing($groupedAction)
        ->assertScript("window.location.hash === '#link-action'", true)
        ->assertScript("document.activeElement.matches('{$groupTrigger}')", true)
        ->assertScript(<<<'JS'
            (() => {
                const dropdown = document.querySelector('[data-testid="action-group"]').closest('.fi-dropdown')
                const data = Alpine.$data(dropdown)
                const trigger = data.getTrigger()
                const panel = data.$refs.panel

                window.destroyedActionMenuCloseCount = 0
                const close = data.close
                data.close = (...parameters) => {
                    window.destroyedActionMenuCloseCount++

                    return close.call(data, ...parameters)
                }

                Alpine.destroyTree(dropdown)
                trigger.setAttribute('aria-controls', 'destroyed')
                document.dispatchEvent(new Event('livewire:navigate'))
                panel.dispatchEvent(new KeyboardEvent('keydown', { key: 'ArrowDown', bubbles: true }))

                return true
            })()
            JS, true)
        ->wait(0.1)
        ->assertAttribute($groupTrigger, 'aria-controls', 'destroyed')
        ->assertScript('window.destroyedActionMenuCloseCount', 0);
});

it('preserves keyboard activation of explicitly styled nested action group triggers', function (): void {
    $trigger = '[data-testid="styled-nested-trigger"]';
    $item = '[data-testid="styled-nested-action"]';

    visit('/notification-browser-test')
        ->keys('[data-testid="responsive-action-group"]:visible', 'Enter')
        ->assertVisible($trigger)
        ->assertScript("!document.querySelector('{$trigger}').hasAttribute('role')", true)
        ->keys($trigger, 'Enter')
        ->assertScript("document.activeElement.matches('{$item}')", true)
        ->keys($item, 'Escape')
        ->assertMissing($item)
        ->assertScript("document.activeElement.matches('{$trigger}')", true)
        ->keys($trigger, ' ')
        ->assertScript("document.activeElement.matches('{$item}')", true)
        ->assertNoSmoke();
});

it('synchronizes a responsive action group trigger across breakpoints', function (): void {
    $trigger = '[data-testid="responsive-action-group"]:visible';
    $item = '[data-testid="responsive-action"]';

    visit('/notification-browser-test')
        ->resize(900, 812)
        ->assertAttribute($trigger, 'aria-haspopup', 'menu')
        ->resize(375, 812)
        ->wait(0.1)
        ->assertAttribute($trigger, 'aria-haspopup', 'menu')
        ->assertScript("(() => { const trigger = Array.from(document.querySelectorAll('[data-testid=\"responsive-action-group\"]')).find((trigger) => trigger.checkVisibility()); return trigger.getAttribute('aria-controls') === trigger.closest('.fi-dropdown').querySelector('.fi-dropdown-panel').id })()", true)
        ->assertScript("(() => { const trigger = Array.from(document.querySelectorAll('[data-testid=\"responsive-action-group\"]')).find((trigger) => trigger.checkVisibility()); return trigger.id === trigger.closest('.fi-dropdown').querySelector('.fi-dropdown-panel').getAttribute('aria-labelledby') })()", true)
        ->assertScript("(() => { const ids = Array.from(document.querySelectorAll('[data-testid=\"responsive-action-group\"]')).map((trigger) => trigger.id).filter(Boolean); return ids.length === new Set(ids).size })()", true)
        ->keys($trigger, 'Enter')
        ->assertScript("document.activeElement.matches('{$item}')", true)
        ->resize(900, 812)
        ->wait(0.1)
        ->assertMissing($item)
        ->assertAttribute($trigger, 'aria-expanded', 'false')
        ->assertScript("(() => { const trigger = Array.from(document.querySelectorAll('[data-testid=\"responsive-action-group\"]')).find((trigger) => trigger.checkVisibility()); return trigger.id === trigger.closest('.fi-dropdown').querySelector('.fi-dropdown-panel').getAttribute('aria-labelledby') })()", true)
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
            ->assertScript("document.activeElement.matches('[data-testid=\"disabled-action\"]')", true)
            ->wait(0.3)
            ->assertNoSmoke()
            ->assertNoAccessibilityIssues();
    }
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

it('preserves a pending dismissal when the notification is destroyed', function (): void {
    foreach ([false, true] as $isDarkMode) {
        $browser = visit('/notification-browser-test');

        if ($isDarkMode) {
            $browser->inDarkMode();
        }

        $page = $browser
            ->click('[data-testid="send-persistent-notification"]')
            ->assertPresent('[x-data^="notificationComponent"]')
            ->assertNoAccessibilityIssues();
        $page->script(<<<'JS'
            window.dismissalCount = 0
            window.addEventListener('notificationClosed', (event) => {
                if (event.detail.id === 'persistent-notification') window.dismissalCount++
            })
            const element = document.querySelector('[x-data^="notificationComponent"]')
            const component = Alpine.$data(element)
            component.close()
            Alpine.destroyTree(element)
            element.remove()
            JS);
        $page->assertScript('window.dismissalCount', 1)
            ->assertNoJavaScriptErrors()
            ->assertNoAccessibilityIssues();
    }
});

it('does not dispatch a pending dismissal after its Livewire host is removed', function (): void {
    $page = visit('/notification-browser-test')
        ->click('[data-testid="send-persistent-notification"]')
        ->assertPresent('[x-data^="notificationComponent"]');

    $page->script(<<<'JS'
        window.dismissalCount = 0
        window.addEventListener('notificationClosed', (event) => {
            if (event.detail.id === 'persistent-notification') window.dismissalCount++
        })
        const element = document.querySelector('[x-data^="notificationComponent"]')
        const host = element.closest('[wire\\:id]')
        const component = Alpine.$data(element)
        component.transitionDuration = 300
        component.close()
        Alpine.destroyTree(host)
        host.remove()
        JS);

    $page->wait(0.5)
        ->assertScript('window.dismissalCount', 0)
        ->assertNoJavaScriptErrors();
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
