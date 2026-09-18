<?php

use Filament\Tests\Actions\TestCase;
use Filament\Tests\Fixtures\Models\User;
use Illuminate\Support\Facades\Artisan;

uses(TestCase::class);

beforeEach(function (): void {
    Artisan::call('filament:assets');
});

describe('browser interactions', function (): void {
    it('prevents `beforeunload` only while actions that can contain unsaved changes are mounted', function (bool $isDarkMode): void {
        $this->actingAs(User::factory()->create());

        $browser = visit('/unsaved-changes-alert-browser-test');

        if ($isDarkMode) {
            $browser->inDarkMode();
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

    it('preserves the `beforeunload` alert for mounted action state without a `hasUnsavedChangesAlert` key', function (): void {
        $this->actingAs(User::factory()->create());

        visit('/unsaved-changes-alert-browser-test')
            ->assertScript(<<<'JS'
                (() => {
                    setUpUnsavedActionChangesAlert({
                        resolveLivewireComponentUsing: () => ({}),
                        $wire: {
                            mountedActions: [{}],
                            __instance: { effects: {} },
                        },
                    })

                    const event = new Event('beforeunload', { cancelable: true })

                    window.dispatchEvent(event)

                    return event.defaultPrevented
                })()
                JS, true)
            ->assertNoSmoke();
    });

    it('can run another action after closing a child opened by an action without a modal', function (bool $isDarkMode): void {
        $this->actingAs(User::factory()->create());

        $browser = visit('/modal-browser-test');

        if ($isDarkMode) {
            $browser->inDarkMode();
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

    it('locks page scroll and restores focus and scroll position after closing a standalone modal', function (): void {
        retry(10, function (): void {
            $this->actingAs(User::factory()->create());

            visit('/modal-browser-test')
                ->assertScript('(() => { const spacer = document.createElement(\'div\'); spacer.style.height = \'200vh\'; document.body.append(spacer); const trigger = document.querySelector(\'[data-testid="standalone-trigger"]\'); trigger.focus({ preventScroll: true }); window.scrollTo(0, document.documentElement.scrollHeight); window.modalTestScrollY = window.scrollY; trigger.click(); return window.modalTestScrollY > 0 })()', true)
                ->assertVisible('[data-testid="standalone-modal"]')
                ->assertScript('document.documentElement.style.overflow', 'hidden')
                ->click('[data-testid="standalone-close"]')
                ->assertMissing('[data-testid="standalone-modal"]')
                ->assertPresent('[data-testid="standalone-trigger"]:focus')
                ->assertScript('window.scrollY === window.modalTestScrollY', true)
                ->assertScript('document.documentElement.style.overflow', '')
                // Let the first modal finish restoring scroll before capturing the position again.
                ->wait(0.5)
                ->assertScript('(() => { window.scrollTo(0, document.documentElement.scrollHeight); window.modalTestScrollY = window.scrollY; document.querySelector(\'[data-testid="standalone-trigger"]\').click(); return window.modalTestScrollY > 0 })()', true)
                ->assertVisible('[data-testid="standalone-modal"]')
                // Let the focus trap activate (it is deferred after opening) before closing.
                ->wait(0.5)
                ->keys('[data-testid="standalone-modal"]', 'Escape')
                ->assertMissing('[data-testid="standalone-modal"]')
                ->assertPresent('[data-testid="standalone-trigger"]:focus')
                ->assertScript('window.scrollY === window.modalTestScrollY', true)
                ->assertScript('document.documentElement.style.overflow', '')
                ->assertNoSmoke();
        });
    });

    it('restores focus and scroll position after cancelling and confirming a top-level action modal', function (): void {
        retry(10, function (): void {
            $this->actingAs(User::factory()->create());

            visit('/modal-browser-test')
                ->assertScript('(() => { const spacer = document.createElement(\'div\'); spacer.style.height = \'200vh\'; document.body.append(spacer); const trigger = document.querySelector(\'[data-testid="basic-trigger"]\'); trigger.focus({ preventScroll: true }); window.scrollTo(0, document.documentElement.scrollHeight); window.modalTestScrollY = window.scrollY; trigger.click(); return window.modalTestScrollY > 0 })()', true)
                ->assertVisible('[data-testid="basic-modal"]')
                ->click('[data-testid="basic-modal"] .fi-modal-footer-actions button >> text=Cancel')
                ->assertMissing('[data-testid="basic-modal"]')
                ->assertPresent('[data-testid="basic-trigger"]:focus')
                ->assertScript('window.scrollY === window.modalTestScrollY', true)
                // Let the first modal finish restoring scroll before capturing the position again.
                ->wait(0.5)
                ->assertScript('(() => { window.scrollTo(0, document.documentElement.scrollHeight); window.modalTestScrollY = window.scrollY; document.querySelector(\'[data-testid="basic-trigger"]\').click(); return window.modalTestScrollY > 0 })()', true)
                ->assertVisible('[data-testid="basic-modal"]')
                // Confirming runs the action and a Livewire request, unlike cancelling.
                ->click('[data-testid="basic-modal"] .fi-modal-footer-actions button >> text=Confirm')
                ->assertMissing('[data-testid="basic-modal"]')
                ->assertPresent('[data-testid="basic-trigger"]:focus')
                ->assertScript('window.scrollY === window.modalTestScrollY', true)
                ->assertNoSmoke();
        });
    });

    it('does not change the page scroll position when opening a standalone modal with no tabbable content', function (bool $isDarkMode): void {
        retry(10, function () use ($isDarkMode): void {
            $this->actingAs(User::factory()->create());

            $browser = visit('/modal-browser-test');

            if ($isDarkMode) {
                $browser->inDarkMode();
            }

            $browser
                ->assertScript('(() => { const spacer = document.createElement(\'div\'); spacer.style.height = \'200vh\'; document.body.append(spacer); const trigger = document.querySelector(\'[data-testid="no-tabbable-content-trigger"]\'); trigger.focus({ preventScroll: true }); window.scrollTo(0, document.documentElement.scrollHeight); window.modalTestScrollY = window.scrollY; trigger.click(); return window.modalTestScrollY > 0 })()', true)
                ->assertVisible('[data-testid="no-tabbable-content-modal"]')
                // Let the focus trap activate (it is deferred after opening) before checking where it put focus.
                ->wait(0.5)
                ->assertPresent('.fi-modal-window-ctn:focus')
                ->assertScript('window.scrollY === window.modalTestScrollY', true)
                ->assertNoSmoke()
                ->assertNoAccessibilityIssues();
        });
    })->with([
        'light mode' => false,
        'dark mode' => true,
    ]);

    it('does not restore focus to the trigger after closing a standalone modal using `:restores-focus="false"`', function (): void {
        retry(10, function (): void {
            $this->actingAs(User::factory()->create());

            visit('/modal-browser-test')
                ->click('[data-testid="no-focus-restore-trigger"]')
                ->assertVisible('[data-testid="no-focus-restore-modal"]')
                ->click('[data-testid="no-focus-restore-close"]')
                ->assertMissing('[data-testid="no-focus-restore-modal"]')
                // Wait out the focus restoration delay before asserting nothing was restored.
                ->wait(1)
                ->assertMissing('[data-testid="no-focus-restore-trigger"]:focus')
                ->assertNoSmoke();
        });
    });

    it('restores focus to the trigger after closing a top-level action modal', function (): void {
        retry(10, function (): void {
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
    });

    it('restores focus when returning from a nested non-overlay action modal', function (): void {
        retry(10, function (): void {
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
    });

    it('restores focus when closing a nested overlay action modal', function (): void {
        retry(10, function (): void {
            $this->actingAs(User::factory()->create());

            visit('/modal-browser-test')
                ->click('Overlay focus restoration')
                ->assertVisible('[data-testid="overlay-modal"]')
                ->click('[data-testid="overlay-modal"] .fi-modal-footer-actions button >> text=Open nested modal')
                ->assertVisible('[data-testid="overlay-modal"]')
                ->assertVisible('[data-testid="overlay-nested-modal"]')
                ->click('[data-testid="overlay-nested-modal"] .fi-modal-footer-actions button >> text=Cancel')
                ->assertMissing('[data-testid="overlay-nested-modal"]')
                ->assertPresent('[data-testid="overlay-nested-trigger"]:focus')
                ->click('[data-testid="overlay-modal"] .fi-modal-footer-actions button >> text=Cancel')
                ->assertMissing('[data-testid="overlay-modal"]')
                ->assertPresent('[data-testid="overlay-trigger"]:focus')
                ->assertNoSmoke();
        });
    });

    it('focuses the modal window instead of the tab-reachable close button when a modal using `closeModalByEscaping(false)` opens', function (): void {
        retry(10, function (): void {
            $this->actingAs(User::factory()->create());

            visit('/modal-browser-test')
                ->click('Escape close disabled')
                ->assertVisible('[data-testid="escape-close-disabled-modal"]')
                ->wait(0.5)
                // The window is autofocused so the close button does not steal focus, while staying in the tab order as the only keyboard way to dismiss the modal.
                ->assertScript('document.activeElement === document.querySelector(\'[data-testid="escape-close-disabled-modal"]\')', true)
                ->assertScript('document.querySelector(\'[data-testid="escape-close-disabled-modal"] .fi-modal-close-btn\').tabIndex', 0)
                ->click('[data-testid="escape-close-disabled-modal"] .fi-modal-close-btn')
                ->assertMissing('[data-testid="escape-close-disabled-modal"]')
                ->assertNoSmoke()
                ->assertNoAccessibilityIssues();

            visit('/modal-browser-test')
                ->inDarkMode()
                ->click('Escape close disabled')
                ->assertVisible('[data-testid="escape-close-disabled-modal"]')
                ->assertNoSmoke()
                ->assertNoAccessibilityIssues();
        });
    });

    it('cancels parent actions and releases the page scroll lock when a nested modal using `cancelParentActionsOnClose()` is dismissed', function (): void {
        retry(10, function (): void {
            $this->actingAs(User::factory()->create());

            visit('/modal-browser-test')
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
                ->assertScript('document.documentElement.style.overflow', '')
                ->assertNoSmoke()
                ->assertNoAccessibilityIssues();

            visit('/modal-browser-test')
                ->inDarkMode()
                ->assertNoSmoke()
                ->assertNoAccessibilityIssues();
        });
    });

    it('keeps the parent modal focus trap active while a nested modal is open', function (): void {
        retry(10, function (): void {
            $this->actingAs(User::factory()->create());

            visit('/modal-browser-test')
                ->click('Scroll preservation')
                ->assertVisible('[data-testid="scroll-modal"]')
                // Let the modal's fields lay out so the window is scrollable.
                ->wait(1)
                // Scroll the parent modal window to the bottom (~1264px).
                ->assertScript('(() => { const el = document.querySelector(\'[data-testid="scroll-modal"]\'); el.scrollTop = el.scrollHeight; return el.scrollTop > 600 })()', true)
                ->click('[data-testid="scroll-modal"] .fi-modal-footer-actions button >> text=Open nested modal')
                ->assertVisible('[data-testid="scroll-nested-modal"]')
                // The parent is now hidden behind the child, but its trap must
                // remain active so it is not re-activated (and re-focused) later.
                ->wait(1)
                ->assertScript('(() => { const el = document.querySelector(\'[data-testid="scroll-modal"]\'); return Alpine.$data(el).isTrapActive === true })()', true)
                ->click('[data-testid="scroll-nested-modal"] .fi-modal-footer-actions button >> text=Cancel')
                ->assertMissing('[data-testid="scroll-nested-modal"]')
                ->assertVisible('[data-testid="scroll-modal"]')
                // The parent modal's scroll position is preserved, not reset to top.
                ->wait(1)
                ->assertScript('(() => { const el = document.querySelector(\'[data-testid="scroll-modal"]\'); return el.scrollTop > 600 })()', true)
                ->assertNoSmoke();
        });
    });

    it('lets clicks reach the page behind a click-through modal without locking page scroll', function (): void {
        retry(10, function (): void {
            $this->actingAs(User::factory()->create());

            visit('/modal-browser-test')
                ->assertSee('Behind: not clicked')
                ->assertScript('document.documentElement.style.overflow', '')
                ->click('[data-testid="click-through-trigger"]')
                ->assertVisible('[data-testid="click-through-modal"]')
                ->assertScript('document.documentElement.style.overflow', '')
                // The open modal covers the page, but because it is click-through
                // the click passes through to the button behind it.
                ->click('[data-testid="behind-button"]')
                ->assertSee('Behind: clicked')
                ->assertNoSmoke();
        });
    });

    it('restores focus after a nested overlay action cancels its parent action', function (): void {
        retry(10, function (): void {
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
});
