<?php

use Filament\Tests\Actions\TestCase;
use Filament\Tests\Fixtures\Models\User;
use Illuminate\Support\Facades\Artisan;

uses(TestCase::class);

beforeEach(function (): void {
    Artisan::call('filament:assets');
});

describe('browser interactions', function (): void {
    it('keeps a loading button focused without allowing repeated activation', function (): void {
        $selector = '[data-testid="loading-button"]';

        foreach ([false, true] as $isDarkMode) {
            $browser = visit('/modal-browser-test');

            if ($isDarkMode) {
                $browser = $browser->inDarkMode();
            }

            $browser
                ->keys($selector, 'Enter')
                ->assertAttribute($selector, 'aria-disabled', 'true')
                ->assertScript('document.querySelector(\'[data-testid="loading-button"]\').disabled', false)
                ->assertPresent("{$selector}:focus")
                ->keys($selector, 'Enter')
                ->keys($selector, 'Space');

            // Playwright normally waits for `aria-disabled` to clear before clicking.
            $browser->page()->locator($selector)->click(['force' => true]);

            $browser
                ->assertAttribute($selector, 'data-activations', '1')
                ->assertScript('!document.querySelector(\'[data-testid="loading-button"]\').hasAttribute("aria-disabled")')
                ->assertPresent("{$selector}:focus")
                ->keys($selector, 'Control+Shift+l')
                ->assertAttribute($selector, 'data-activations', '2')
                ->assertScript('!document.querySelector(\'[data-testid="loading-button"]\').hasAttribute("aria-disabled")')
                ->assertNoSmoke()
                ->assertNoAccessibilityIssues();
        }
    });

    it('prevents `beforeunload` only while actions that can contain unsaved changes are mounted', function (bool $isDarkMode): void {
        $this->actingAs(User::factory()->create());

        $browser = visit('/unsaved-changes-alert-browser-test');

        if ($isDarkMode) {
            $browser = $browser->inDarkMode();
        }

        $dispatchBeforeUnloadEvent = <<<'JS'
            (() => {
                const event = new Event('beforeunload', { cancelable: true })

                window.dispatchEvent(event)

                return event.defaultPrevented
            })()
            JS;

        $browser
            ->assertScript($dispatchBeforeUnloadEvent, false)
            ->click('[data-testid="read-only-unsaved-changes-alert-trigger"]')
            ->assertVisible('[data-testid="read-only-unsaved-changes-alert-modal"]')
            ->assertScript($dispatchBeforeUnloadEvent, false)
            ->assertNoAccessibilityIssues()
            ->click('[data-testid="read-only-unsaved-changes-alert-modal"] .fi-modal-footer-actions button >> text=Cancel')
            ->assertMissing('[data-testid="read-only-unsaved-changes-alert-modal"]')
            ->click('[data-testid="editable-unsaved-changes-alert-trigger"]')
            ->assertVisible('[data-testid="editable-unsaved-changes-alert-modal"]')
            ->assertScript($dispatchBeforeUnloadEvent, true)
            ->click('[data-testid="editable-unsaved-changes-alert-modal"] .fi-modal-footer-actions button >> text=Cancel')
            ->assertMissing('[data-testid="editable-unsaved-changes-alert-modal"]')
            ->click('[data-testid="nested-unsaved-changes-alert-trigger"]')
            ->assertVisible('[data-testid="nested-unsaved-changes-alert-modal"]')
            ->assertScript($dispatchBeforeUnloadEvent, false)
            ->click('[data-testid="nested-unsaved-changes-alert-modal"] .fi-modal-footer-actions button >> text=Open editable nested action')
            ->assertVisible('[data-testid="editable-nested-unsaved-changes-alert-modal"]')
            ->assertScript($dispatchBeforeUnloadEvent, true)
            ->assertNoAccessibilityIssues()
            ->click('[data-testid="editable-nested-unsaved-changes-alert-modal"] .fi-modal-footer-actions button >> text=Cancel')
            ->assertVisible('[data-testid="nested-unsaved-changes-alert-modal"]')
            ->assertScript($dispatchBeforeUnloadEvent, false)
            ->assertNoSmoke();
    })->with([
        'light mode' => false,
        'dark mode' => true,
    ]);

    it('can run another action after closing a child opened by an action without a modal', function (bool $isDarkMode): void {
        $this->actingAs(User::factory()->create());

        $browser = visit('/modal-browser-test');

        if ($isDarkMode) {
            $browser = $browser->inDarkMode();
        }

        $browser
            ->click('[data-testid="modal-less-parent-trigger"]')
            ->assertVisible('[data-testid="modal-less-parent-child-modal"]')
            ->click('[data-testid="modal-less-parent-child-modal"] .fi-modal-footer-actions button >> text=Cancel')
            ->assertMissing('[data-testid="modal-less-parent-child-modal"]')
            ->assertNoAccessibilityIssues()
            ->click('[data-testid="action-after-child-trigger"]')
            ->assertSeeIn('[data-testid="action-after-child-result"]', 'ran')
            ->assertNoSmoke();
    })->with([
        'light mode' => false,
        'dark mode' => true,
    ]);

    it('keeps the parent modal open when `getValidatedData()` prevents a nested modal from opening', function (): void {
        $this->actingAs(User::factory()->create());

        $browser = visit('/modal-browser-test');

        $assertValidationBehavior = static function ($browser): void {
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
        };

        $assertValidationBehavior($browser);

        $browser
            ->click('[data-testid="validated-parent-data-nested-modal"] .fi-modal-footer-actions button >> text=Cancel')
            ->click('[data-testid="validated-parent-data-modal"] .fi-modal-footer-actions button >> text=Cancel');

        $browser = $browser->inDarkMode();

        $assertValidationBehavior($browser);
    });

    it('restores standalone modal state on close', function (): void {
        $this->actingAs(User::factory()->create());

        $page = visit('/modal-browser-test?scroll=1')
            ->keys('[data-testid="standalone-trigger"]', 'Control+End')
            ->assertScript('window.scrollY === document.documentElement.scrollHeight - window.innerHeight', true);
        $scrollPosition = $page->script('window.scrollY');

        $page->keys('[data-testid="standalone-trigger"]', 'Enter')
            ->assertVisible('[data-testid="standalone-modal"]')
            ->keys('[data-testid="standalone-close"]', 'PageDown')
            ->assertScript('window.scrollY', $scrollPosition)
            ->click('[data-testid="standalone-close"]')
            ->assertMissing('[data-testid="standalone-modal"]')
            ->assertPresent('[data-testid="standalone-trigger"]:focus')
            ->assertScript('window.scrollY', $scrollPosition)
            ->keys('[data-testid="standalone-trigger"]', 'PageUp')
            ->assertScript('window.scrollY < ' . $scrollPosition, true)
            ->keys('[data-testid="standalone-trigger"]', 'Control+End')
            ->keys('[data-testid="standalone-trigger"]', 'Enter')
            ->assertVisible('[data-testid="standalone-modal"]')
            ->assertPresent('[data-testid="standalone-close"]:focus')
            ->keys(':focus', 'Escape')
            ->assertMissing('[data-testid="standalone-modal"]')
            ->assertPresent('[data-testid="standalone-trigger"]:focus')
            ->assertScript('window.scrollY', $scrollPosition)
            ->assertNoSmoke()
            ->assertNoAccessibilityIssues();

        // `inDarkMode()` requires a fresh browser context.
        visit('/modal-browser-test')
            ->inDarkMode()
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
    });

    it('does not change the page scroll position when opening a standalone modal with no tabbable content', function (bool $isDarkMode): void {
        $this->actingAs(User::factory()->create());

        $browser = visit('/modal-browser-test?scroll=1');

        if ($isDarkMode) {
            $browser = $browser->inDarkMode();
        }

        $browser->keys('[data-testid="no-tabbable-content-trigger"]', 'Control+End')
            ->assertScript('window.scrollY === document.documentElement.scrollHeight - window.innerHeight', true);
        $scrollPosition = $browser->script('window.scrollY');

        $browser->keys('[data-testid="no-tabbable-content-trigger"]', 'Enter')
            ->assertVisible('[data-testid="no-tabbable-content-modal"]')
            ->assertScript('window.scrollY', $scrollPosition)
            ->assertNoSmoke()
            ->assertNoAccessibilityIssues();
    })->with([
        'light mode' => false,
        'dark mode' => true,
    ]);

    it('allows keyboard scrolling in a long modal without tabbable content', function (string $modalType, bool $isDarkMode, string $scrollKey): void {
        $this->actingAs(User::factory()->create());

        $browser = visit('/modal-browser-test?scroll=1');

        if ($isDarkMode) {
            $browser = $browser->inDarkMode();
        }

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
            ->assertNoSmoke()
            ->assertNoAccessibilityIssues()
            ->keys($modalSelector, 'Escape')
            ->assertMissing($modalSelector)
            ->assertPresent("{$triggerSelector}:focus")
            ->assertScript('window.scrollY', $scrollPosition);
    })->with([
        'sticky modal in light mode' => ['sticky', false],
        'sticky modal in dark mode' => ['sticky', true],
        'slide-over in light mode' => ['slide-over', false],
        'slide-over in dark mode' => ['slide-over', true],
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

    it('restores focus to the trigger after closing a top-level action modal', function (): void {
        $this->actingAs(User::factory()->create());

        visit('/modal-browser-test')
            ->click('Modal focus restoration')
            ->assertVisible('[data-testid="basic-modal"]')
            ->click('[data-testid="basic-modal"] .fi-modal-footer-actions button >> text=Cancel')
            ->assertMissing('[data-testid="basic-modal"]')
            ->assertPresent('[data-testid="basic-trigger"]:focus')
            ->assertNoSmoke()
            ->assertNoAccessibilityIssues();

        visit('/modal-browser-test')
            ->inDarkMode()
            ->assertNoSmoke()
            ->assertNoAccessibilityIssues();
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

        foreach ([$page, $page->inDarkMode()] as $themedPage) {
            $themedPage
                ->click('[data-testid="escape-close-disabled-trigger"]')
                ->assertVisible($modalSelector)
                ->assertPresent($modalSelector . ':focus')
                ->keys($modalSelector, 'Tab')
                ->assertPresent($closeButtonSelector . ':focus')
                ->assertNoAccessibilityIssues()
                ->keys($closeButtonSelector, 'Enter')
                ->assertMissing($modalSelector)
                ->assertNoSmoke();
        }
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
            ->assertNoSmoke()
            ->assertNoAccessibilityIssues();

        visit('/modal-browser-test')
            ->inDarkMode()
            ->assertNoSmoke()
            ->assertNoAccessibilityIssues();
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
            ->assertScript('document.activeElement.closest(\'[data-testid="scroll-modal"]\') !== null', true)
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
