<?php

use Filament\Tests\Actions\TestCase;
use Filament\Tests\Fixtures\Models\Department;
use Filament\Tests\Fixtures\Models\Ticket;
use Filament\Tests\Fixtures\Models\User;
use Illuminate\Support\Facades\Artisan;

uses(TestCase::class);

beforeEach(function (): void {
    Artisan::call('filament:assets');
});

describe('browser interactions', function (): void {
    it('keeps a loading button focused without allowing repeated activation', function (): void {
        $selector = '[data-testid="loading-button"]';

        $browser = visit('/modal-browser-test');

        $browser
            ->keys($selector, 'Enter')
            ->assertAttribute($selector, 'aria-disabled', 'true')
            ->assertAttributeMissing($selector, 'disabled')
            ->assertPresent("{$selector}:focus")
            ->keys($selector, 'Enter')
            ->keys($selector, 'Space');

        $browser
            ->assertAttribute($selector, 'data-activations', '1')
            ->assertAttributeMissing($selector, 'aria-disabled')
            ->assertPresent("{$selector}:focus")
            ->keys($selector, 'Control+Shift+l')
            ->assertAttribute($selector, 'data-activations', '2')
            ->assertAttributeMissing($selector, 'aria-disabled')
            ->assertNoSmoke()
            ->assertNoAccessibilityIssues();

        $browser->inDarkMode()->assertNoAccessibilityIssues();
    });

    it('cancels `beforeunload` events only while actions that can contain unsaved changes are mounted', function (): void {
        $this->actingAs(User::factory()->create());

        $browser = visit('/unsaved-changes-alert-browser-test');

        $dispatchBeforeUnloadEvent = <<<'JS'
            (() => {
                const event = new Event('beforeunload', { cancelable: true })

                window.dispatchEvent(event)

                return event.defaultPrevented
            })()
            JS;

        expect($browser->script($dispatchBeforeUnloadEvent))->toBeFalse();

        $browser->click('[data-testid="read-only-unsaved-changes-alert-trigger"]')
            ->assertVisible('[data-testid="read-only-unsaved-changes-alert-modal"]');

        expect($browser->script($dispatchBeforeUnloadEvent))->toBeFalse();

        $browser->click('[data-testid="read-only-unsaved-changes-alert-modal"] .fi-modal-footer-actions button >> text=Cancel')
            ->assertMissing('[data-testid="read-only-unsaved-changes-alert-modal"]')
            ->click('[data-testid="editable-unsaved-changes-alert-trigger"]')
            ->assertVisible('[data-testid="editable-unsaved-changes-alert-modal"]');

        expect($browser->script($dispatchBeforeUnloadEvent))->toBeTrue();

        $browser->click('[data-testid="editable-unsaved-changes-alert-modal"] .fi-modal-footer-actions button >> text=Cancel')
            ->assertMissing('[data-testid="editable-unsaved-changes-alert-modal"]')
            ->click('[data-testid="nested-unsaved-changes-alert-trigger"]')
            ->assertVisible('[data-testid="nested-unsaved-changes-alert-modal"]');

        expect($browser->script($dispatchBeforeUnloadEvent))->toBeFalse();

        $browser->click('[data-testid="nested-unsaved-changes-alert-modal"] .fi-modal-footer-actions button >> text=Open editable nested action')
            ->assertVisible('[data-testid="editable-nested-unsaved-changes-alert-modal"]');

        expect($browser->script($dispatchBeforeUnloadEvent))->toBeTrue();

        $browser->click('[data-testid="editable-nested-unsaved-changes-alert-modal"] .fi-modal-footer-actions button >> text=Cancel')
            ->assertVisible('[data-testid="nested-unsaved-changes-alert-modal"]')
            ->assertNoSmoke();

        expect($browser->script($dispatchBeforeUnloadEvent))->toBeFalse();
    });

    it('can run another action after closing a child opened by an action without a modal', function (): void {
        $this->actingAs(User::factory()->create());

        $browser = visit('/modal-browser-test');

        $browser
            ->click('[data-testid="modal-less-parent-trigger"]')
            ->assertVisible('[data-testid="modal-less-parent-child-modal"]')
            ->click('[data-testid="modal-less-parent-child-modal"] .fi-modal-footer-actions button >> text=Cancel')
            ->assertMissing('[data-testid="modal-less-parent-child-modal"]')
            ->click('[data-testid="action-after-child-trigger"]')
            ->assertSeeIn('[data-testid="action-after-child-result"]', 'ran')
            ->assertNoSmoke();
    });

    it('keeps the parent modal open when `getValidatedData()` prevents a nested modal from opening', function (): void {
        $this->actingAs(User::factory()->create());

        $browser = visit('/modal-browser-test');

        $browser
            ->click('[data-testid="validated-parent-data-trigger"]')
            ->assertVisible('[data-testid="validated-parent-data-modal"]')
            ->click('[data-testid="validated-parent-data-suffix-action"]')
            ->assertValue('[data-testid="validated-parent-data-input"]', 'First generated name')
            ->click('[data-testid="validated-parent-data-suffix-action"]')
            ->assertValue('[data-testid="validated-parent-data-input"]', 'Second generated name')
            ->fill('[data-testid="validated-parent-data-input"]', '')
            ->click('[data-testid="validated-parent-data-nested-trigger"]')
            ->assertVisible('[data-testid="validated-parent-data-modal"]')
            ->assertMissing('[data-testid="validated-parent-data-nested-modal"]')
            ->assertVisible('[data-testid="validated-parent-data-field"] [data-validation-error]')
            ->assertNoAccessibilityIssues()
            ->type('[data-testid="validated-parent-data-input"]', 'Jane Doe')
            ->click('[data-testid="validated-parent-data-nested-trigger"]')
            ->assertValue('[data-testid="validated-parent-data-input"]', 'Jane Doe')
            ->assertVisible('[data-testid="validated-parent-data-nested-modal"]')
            ->assertNoSmoke()
            ->assertNoAccessibilityIssues();

        $browser
            ->click('[data-testid="validated-parent-data-nested-modal"] .fi-modal-footer-actions button >> text=Cancel')
            ->click('[data-testid="validated-parent-data-modal"] .fi-modal-footer-actions button >> text=Cancel');

        $browser->inDarkMode()
            ->click('[data-testid="validated-parent-data-trigger"]')
            ->assertVisible('[data-testid="validated-parent-data-modal"]')
            ->fill('[data-testid="validated-parent-data-input"]', '')
            ->click('[data-testid="validated-parent-data-nested-trigger"]')
            ->assertVisible('[data-testid="validated-parent-data-field"] [data-validation-error]')
            ->assertNoAccessibilityIssues()
            ->type('[data-testid="validated-parent-data-input"]', 'Jane Doe')
            ->click('[data-testid="validated-parent-data-nested-trigger"]')
            ->assertVisible('[data-testid="validated-parent-data-nested-modal"]')
            ->assertNoAccessibilityIssues();
    });

    it('restores standalone modal state on close', function (): void {
        $this->actingAs(User::factory()->create());

        $page = visit('/modal-browser-test?scroll=1')
            ->keys('[data-testid="standalone-trigger"]', 'Control+End')
            ->assertScript('window.scrollY === document.documentElement.scrollHeight - window.innerHeight', true);
        $scrollPosition = $page->script('window.scrollY');

        $page->keys('[data-testid="standalone-trigger"]', 'Enter')
            ->assertVisible('[data-testid="standalone-modal"]')
            ->assertPresent('[data-testid="standalone-close"]:focus')
            ->keys(':focus', 'PageDown')
            ->assertScript('window.scrollY', $scrollPosition)
            ->click('[data-testid="standalone-close"]')
            ->assertMissing('[data-testid="standalone-modal"]')
            ->assertPresent('[data-testid="standalone-trigger"]:focus')
            ->assertScript('window.scrollY', $scrollPosition)
            ->keys('[data-testid="standalone-trigger"]', 'PageUp')
            ->assertScript('window.scrollY < ' . $scrollPosition, true)
            ->keys('[data-testid="standalone-trigger"]', 'Control+End')
            ->assertScript('window.scrollY', $scrollPosition)
            ->keys('[data-testid="standalone-trigger"]', 'Enter')
            ->assertVisible('[data-testid="standalone-modal"]')
            ->assertPresent('[data-testid="standalone-close"]:focus')
            ->assertNoAccessibilityIssues()
            ->keys(':focus', 'Escape')
            ->assertMissing('[data-testid="standalone-modal"]')
            ->assertPresent('[data-testid="standalone-trigger"]:focus')
            ->assertScript('window.scrollY', $scrollPosition)
            ->assertNoSmoke();

        visit('/modal-browser-test')
            ->inDarkMode()
            ->click('[data-testid="standalone-trigger"]')
            ->assertVisible('[data-testid="standalone-modal"]')
            ->assertPresent('[data-testid="standalone-close"]:focus')
            ->assertNoAccessibilityIssues();
    });

    it('restores focus and scroll position after cancelling and confirming a top-level action modal', function (): void {
        $this->actingAs(User::factory()->create());

        $page = visit('/modal-browser-test?scroll=1')
            ->keys('[data-testid="basic-trigger"]', 'Control+End')
            ->assertScript('window.scrollY === document.documentElement.scrollHeight - window.innerHeight', true);
        $scrollPosition = $page->script('window.scrollY');

        $page->keys('[data-testid="basic-trigger"]', 'Enter')
            ->assertVisible('[data-testid="basic-modal"]')
            ->assertPresent('[data-testid="basic-modal"] :focus')
            ->assertNoAccessibilityIssues()
            ->click('[data-testid="basic-modal"] .fi-modal-footer-actions button >> text=Cancel')
            ->assertMissing('[data-testid="basic-modal"]')
            ->assertPresent('[data-testid="basic-trigger"]:focus')
            ->assertScript('window.scrollY', $scrollPosition)
            ->keys('[data-testid="basic-trigger"]', 'Enter')
            ->assertVisible('[data-testid="basic-modal"]')
            ->assertPresent('[data-testid="basic-modal"] :focus')
            // Confirming runs the action and a Livewire request, unlike cancelling.
            ->click('[data-testid="basic-modal"] .fi-modal-footer-actions button >> text=Confirm')
            ->assertMissing('[data-testid="basic-modal"]')
            ->assertPresent('[data-testid="basic-trigger"]:focus')
            ->assertScript('window.scrollY', $scrollPosition)
            ->assertNoSmoke();

        visit('/modal-browser-test')->inDarkMode()
            ->click('[data-testid="basic-trigger"]')
            ->assertVisible('[data-testid="basic-modal"]')
            ->assertNoAccessibilityIssues();
    });

    it('does not change the page scroll position when opening a standalone modal with no tabbable content', function (): void {
        $this->actingAs(User::factory()->create());

        $browser = visit('/modal-browser-test?scroll=1');

        $browser->keys('[data-testid="no-tabbable-content-trigger"]', 'Control+End')
            ->assertScript('window.scrollY === document.documentElement.scrollHeight - window.innerHeight', true);
        $scrollPosition = $browser->script('window.scrollY');

        $browser->keys('[data-testid="no-tabbable-content-trigger"]', 'Enter')
            ->assertVisible('[data-testid="no-tabbable-content-modal"]')
            ->assertScript('window.scrollY', $scrollPosition)
            ->assertNoSmoke()
            ->assertNoAccessibilityIssues();

        $browser->inDarkMode()
            ->click('[data-testid="no-tabbable-content-trigger"]')
            ->assertVisible('[data-testid="no-tabbable-content-modal"]')
            ->assertNoAccessibilityIssues();
    });

    it('allows keyboard scrolling in a long modal without tabbable content', function (string $modalType, string $scrollKey): void {
        $this->actingAs(User::factory()->create());

        $browser = visit('/modal-browser-test?scroll=1');

        $modalSelector = "[data-testid=\"long-{$modalType}-modal\"]";
        $triggerSelector = "[data-testid=\"long-{$modalType}-trigger\"]";

        $browser->keys($triggerSelector, 'Control+End')
            ->assertScript('window.scrollY === document.documentElement.scrollHeight - window.innerHeight', true);
        $scrollPosition = $browser->script('window.scrollY');

        $browser->keys($triggerSelector, 'Enter')
            ->assertVisible($modalSelector)
            ->assertAttribute($modalSelector, 'tabindex', '0')
            ->assertPresent("{$modalSelector}:focus")
            ->assertScript('window.scrollY', $scrollPosition)
            ->keys(':focus', $scrollKey)
            ->assertScript("document.querySelector('{$modalSelector}').scrollTop > 0", true)
            ->keys(':focus', 'Control+Home')
            ->assertScript("document.querySelector('{$modalSelector}').scrollTop", 0)
            ->assertPresent("{$modalSelector}:focus")
            ->assertNoSmoke();

        if ($scrollKey === 'PageDown') {
            $browser->assertNoAccessibilityIssues();
        }

        $browser
            ->keys($modalSelector, 'Escape')
            ->assertMissing($modalSelector)
            ->assertPresent("{$triggerSelector}:focus")
            ->assertScript('window.scrollY', $scrollPosition);

        if ($scrollKey === 'PageDown') {
            $browser->inDarkMode()
                ->click($triggerSelector)
                ->assertVisible($modalSelector)
                ->assertNoAccessibilityIssues();
        }
    })->with([
        'sticky modal' => ['sticky'],
        'slide-over' => ['slide-over'],
    ])->with(['PageDown', 'ArrowDown', 'Space']);

    it('does not restore focus to the trigger after closing a standalone modal using `:restores-focus="false"`', function (): void {
        $this->actingAs(User::factory()->create());

        visit('/modal-browser-test')
            ->click('[data-testid="no-focus-restore-trigger"]')
            ->assertVisible('[data-testid="no-focus-restore-modal"]')
            ->click('[data-testid="no-focus-restore-close"]')
            ->assertMissing('[data-testid="no-focus-restore-modal"]')
            ->assertMissing('[data-testid="no-focus-restore-trigger"]:focus')
            ->assertNoSmoke();
    });

    it('restores focus when returning from a nested non-overlay action modal', function (): void {
        $this->actingAs(User::factory()->create());

        visit('/modal-browser-test')
            ->click('Non-overlay focus restoration')
            ->assertVisible('[data-testid="non-overlay-modal"]')
            ->click('[data-testid="non-overlay-modal"] .fi-modal-footer-actions button >> text=Open nested modal')
            ->assertVisible('[data-testid="non-overlay-nested-modal"]')
            ->assertMissing('[data-testid="non-overlay-modal"]')
            ->click('[data-testid="non-overlay-nested-modal"] .fi-modal-footer-actions button >> text=Cancel')
            ->assertVisible('[data-testid="non-overlay-modal"]')
            ->assertPresent('[data-testid="non-overlay-nested-trigger"]:focus')
            ->click('[data-testid="non-overlay-modal"] .fi-modal-footer-actions button >> text=Cancel')
            ->assertMissing('[data-testid="non-overlay-modal"]')
            ->assertPresent('[data-testid="non-overlay-trigger"]:focus')
            ->assertNoSmoke();
    });

    it('restores focus when closing a nested overlay action modal', function (): void {
        $this->actingAs(User::factory()->create());

        visit('/modal-browser-test')
            ->click('Overlay focus restoration')
            ->assertVisible('[data-testid="overlay-modal"]')
            ->assertPresent('[data-testid="overlay-modal"] :focus')
            ->click('[data-testid="overlay-modal"] .fi-modal-footer-actions button >> text=Open nested modal')
            ->assertVisible('[data-testid="overlay-modal"]')
            ->assertVisible('[data-testid="overlay-nested-modal"]')
            ->assertPresent('[data-testid="overlay-nested-modal"] :focus')
            ->click('[data-testid="overlay-nested-modal"] .fi-modal-footer-actions button >> text=Cancel')
            ->assertMissing('[data-testid="overlay-nested-modal"]')
            ->assertPresent('[data-testid="overlay-nested-trigger"]:focus')
            ->click('[data-testid="overlay-modal"] .fi-modal-footer-actions button >> text=Cancel')
            ->assertMissing('[data-testid="overlay-modal"]')
            ->assertPresent('[data-testid="overlay-trigger"]:focus')
            ->assertNoSmoke();
    });

    it('keeps the close button keyboard reachable when a modal uses `closeModalByEscaping(false)`', function (): void {
        $this->actingAs(User::factory()->create());

        $page = visit('/modal-browser-test');
        $modalSelector = '[data-testid="escape-close-disabled-modal"]';
        $closeButtonSelector = $modalSelector . ' .fi-modal-close-btn';

        $page
            ->click('[data-testid="escape-close-disabled-trigger"]')
            ->assertVisible($modalSelector)
            ->assertPresent($modalSelector . ':focus')
            ->keys($modalSelector, 'Tab')
            ->assertPresent($closeButtonSelector . ':focus')
            ->assertNoAccessibilityIssues()
            ->keys($closeButtonSelector, 'Enter')
            ->assertMissing($modalSelector)
            ->assertNoSmoke();

        $page->inDarkMode()
            ->click('[data-testid="escape-close-disabled-trigger"]')
            ->assertVisible($modalSelector)
            ->assertNoAccessibilityIssues();
    });

    it('cancels parent actions and releases the page scroll lock when a nested modal using `cancelParentActionsOnClose()` is dismissed', function (): void {
        $this->actingAs(User::factory()->create());

        visit('/modal-browser-test?scroll=1')
            ->click('Cancel parents on close')
            ->assertVisible('[data-testid="cancel-on-close-modal"]')
            ->click('[data-testid="cancel-on-close-modal"] .fi-modal-footer-actions button >> text=Open level 2')
            ->assertVisible('[data-testid="cancel-on-close-parent-modal"]')
            ->click('[data-testid="cancel-on-close-parent-modal"] .fi-modal-footer-actions button >> text=Open level 3')
            ->assertVisible('[data-testid="cancel-on-close-nested-modal"]')
            ->click('[data-testid="cancel-on-close-nested-modal"] .fi-modal-close-btn')
            ->assertMissing('[data-testid="cancel-on-close-nested-modal"]')
            ->assertMissing('[data-testid="cancel-on-close-parent-modal"]')
            ->assertMissing('[data-testid="cancel-on-close-modal"]')
            ->assertPresent('[data-testid="cancel-on-close-trigger"]:focus')
            ->keys('[data-testid="cancel-on-close-trigger"]', 'Control+End')
            ->assertScript('window.scrollY > 0', true)
            ->assertNoSmoke();
    });

    it('keeps focus trapped and preserves parent scroll after closing a nested modal', function (): void {
        $this->actingAs(User::factory()->create());

        visit('/modal-browser-test')
            ->click('Scroll preservation')
            ->assertVisible('[data-testid="scroll-modal"]')
            ->assertPresent('[data-testid="scroll-field-1"]:focus')
            ->keys('[data-testid="scroll-field-1"]', 'Tab')
            ->assertPresent('[data-testid="scroll-field-2"]:focus')
            ->keys('[data-testid="scroll-field-1"]', 'Shift+Tab')
            ->assertPresent('[data-testid="scroll-modal"] :focus')
            ->keys('[data-testid="scroll-modal"]', 'Control+End')
            ->assertScript('(() => { const modal = document.querySelector(\'[data-testid="scroll-modal"]\'); return modal.scrollTop === modal.scrollHeight - modal.clientHeight })()', true)
            ->click('[data-testid="scroll-nested-trigger"]')
            ->assertVisible('[data-testid="scroll-nested-modal"]')
            ->assertPresent('[data-testid="scroll-nested-modal"] :focus')
            ->click('[data-testid="scroll-nested-cancel"]')
            ->assertMissing('[data-testid="scroll-nested-modal"]')
            ->assertVisible('[data-testid="scroll-modal"]')
            ->assertPresent('[data-testid="scroll-nested-trigger"]:focus')
            ->assertScript('(() => { const modal = document.querySelector(\'[data-testid="scroll-modal"]\'); return modal.scrollTop === modal.scrollHeight - modal.clientHeight })()', true)
            ->assertNoSmoke();
    });

    it('lets clicks reach the page behind a click-through modal without locking page scroll', function (): void {
        $this->actingAs(User::factory()->create());

        visit('/modal-browser-test?scroll=1')
            ->assertSee('Behind: not clicked')
            ->click('[data-testid="click-through-trigger"]')
            ->assertVisible('[data-testid="click-through-modal"]')
            // The open modal covers the page, but because it is click-through
            // the click passes through to the button behind it.
            ->click('[data-testid="behind-button"]')
            ->assertSee('Behind: clicked')
            ->keys('[data-testid="behind-button"]', 'PageDown')
            ->assertScript('window.scrollY > 0', true)
            ->assertNoSmoke();
    });

    it('restores focus after a nested overlay action cancels its parent action', function (): void {
        $this->actingAs(User::factory()->create());

        visit('/modal-browser-test')
            ->click('Overlay focus restoration')
            ->assertVisible('[data-testid="overlay-modal"]')
            ->click('[data-testid="overlay-modal"] .fi-modal-footer-actions button >> text=Close all')
            ->assertVisible('[data-testid="overlay-modal"]')
            ->assertVisible('[data-testid="overlay-cancel-modal"]')
            ->click('[data-testid="overlay-cancel-modal"] .fi-modal-footer-actions button >> text=Confirm')
            ->assertMissing('[data-testid="overlay-cancel-modal"]')
            ->assertMissing('[data-testid="overlay-modal"]')
            ->assertPresent('[data-testid="overlay-trigger"]:focus')
            ->assertNoSmoke();
    });
});

describe('action deep links', function (): void {
    it('routes an ordinary action deep link away from a page with the same action name', function (): void {
        $ticket = Ticket::factory()->hasAttached(Department::factory())->create();
        $browser = visit('/scoped-actions-browser-test?' . http_build_query([
            'ticket' => $ticket->getKey(),
            'tab' => 'summary',
            'actionComponent' => 'departments',
            'action' => 'view',
        ]));

        $browser->click('[data-testid="page-view-trigger"]')
            ->assertVisible('[data-testid="page-modal"]')
            ->click('[data-testid="dismiss-ticket-overview"]')
            ->assertMissing('[data-testid="page-modal"]')
            ->assertQueryStringHas('action', 'view')
            ->assertQueryStringHas('actionComponent', 'departments')
            ->click('[role="tab"] >> text=Departments')
            ->assertVisible('[data-testid="departments-modal"]')
            ->assertMissing('[data-testid="other-departments-modal"]')
            ->assertMissing('[data-testid="page-modal"]')
            ->click('[data-testid="dismiss-department-overview"]')
            ->assertMissing('[data-testid="departments-modal"]')
            ->click('[role="tab"] >> text=Summary')
            ->click('[role="tab"] >> text=Departments')
            ->assertMissing('[data-testid="departments-modal"]')
            ->assertMissing('[data-testid="other-departments-modal"]')
            ->assertNoSmoke();
    });

    it('opens only the targeted lazy embedded action and permits closing and reopening it', function (): void {
        $ticket = Ticket::factory()->hasAttached(Department::factory())->create();
        $department = $ticket->departments->first();
        $browser = visit('/scoped-actions-browser-test?' . http_build_query([
            'ticket' => $ticket->getKey(),
            'tab' => 'departments',
            'actionComponent' => 'departments',
            'tableAction' => 'view',
            'tableActionRecord' => $department->getKey(),
        ]));

        $browser->assertVisible('[data-testid="departments-table-modal"]')
            ->assertMissing('[data-testid="other-departments-table-modal"]')
            ->assertMissing('[data-testid="page-modal"]')
            ->assertNoAccessibilityIssues();

        $browser->inDarkMode()
            ->assertVisible('[data-testid="departments-table-modal"]')
            ->assertNoAccessibilityIssues();

        $browser->click('[data-testid="dismiss-department-record"]')
            ->assertMissing('[data-testid="departments-table-modal"]')
            ->click('[data-testid="departments-view-trigger"]')
            ->assertVisible('[data-testid="departments-table-modal"]')
            ->click('[data-testid="dismiss-department-record"]')
            ->assertMissing('[data-testid="departments-table-modal"]')
            ->click('[data-testid="toggle-departments"]')
            ->assertMissing('[data-testid="departments-view-trigger"]')
            ->click('[data-testid="toggle-departments"]')
            ->assertVisible('[data-testid="departments-table-modal"]')
            ->assertNoSmoke();
    });

    it('consumes a fresh deep link after SPA navigation without reopening it during the same lifetime', function (): void {
        $ticket = Ticket::factory()->hasAttached(Department::factory())->create();
        $browser = visit('/spa/scoped-actions-browser-test?ticket=' . $ticket->getKey());

        $browser->click('[data-testid="department-deep-link"]')
            ->assertVisible('[data-testid="departments-modal"]')
            ->assertMissing('[data-testid="page-modal"]')
            ->click('[data-testid="dismiss-department-overview"]')
            ->assertMissing('[data-testid="departments-modal"]')
            ->click('[data-testid="department-deep-link"]')
            ->assertVisible('[data-testid="departments-modal"]')
            ->assertNoSmoke();
    });
});
