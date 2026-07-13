<?php

use Filament\Tests\Actions\TestCase;
use Filament\Tests\Fixtures\Models\User;
use Illuminate\Support\Facades\Artisan;

uses(TestCase::class);

beforeEach(function (): void {
    Artisan::call('filament:assets');
});

describe('browser interactions', function (): void {
    it('restores focus to the trigger after closing a standalone modal', function (): void {
        retry(10, function (): void {
            $this->actingAs(User::factory()->create());

            visit('/modal-browser-test')
                ->assertSee('Modal Browser Test')
                ->click('[data-testid="standalone-trigger"]')
                ->assertVisible('[data-testid="standalone-modal"]')
                ->click('[data-testid="standalone-close"]')
                ->assertMissing('[data-testid="standalone-modal"]')
                ->assertPresent('[data-testid="standalone-trigger"]:focus')
                ->assertNoSmoke();
        });
    });

    it('restores focus to the trigger after closing a top-level action modal', function (): void {
        retry(10, function (): void {
            $this->actingAs(User::factory()->create());

            visit('/modal-browser-test')
                ->assertSee('Modal Browser Test')
                ->click('Modal focus restoration')
                ->assertVisible('[data-testid="basic-modal"]')
                ->click('[data-testid="basic-modal"] .fi-modal-footer-actions button >> text=Cancel')
                ->assertMissing('[data-testid="basic-modal"]')
                ->assertPresent('[data-testid="basic-trigger"]:focus')
                ->assertNoSmoke()
                ->assertNoAccessibilityIssues();

            visit('/modal-browser-test')
                ->inDarkMode()
                ->assertNoAccessibilityIssues();
        });
    });

    it('restores focus when returning from a nested non-overlay action modal', function (): void {
        retry(10, function (): void {
            $this->actingAs(User::factory()->create());

            visit('/modal-browser-test')
                ->assertSee('Modal Browser Test')
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
                ->assertSee('Modal Browser Test')
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

    it('focuses the first form control instead of the tab-reachable close button when a modal using `closeModalByEscaping(false)` opens', function (): void {
        retry(10, function (): void {
            $this->actingAs(User::factory()->create());

            visit('/modal-browser-test')
                ->assertSee('Modal Browser Test')
                ->click('Escape close disabled')
                ->assertVisible('[data-testid="escape-close-disabled-modal"]')
                ->wait(0.5)
                ->assertScript('document.activeElement === document.querySelector(\'input[wire\\\\:model="mountedActions.0.data.name"]\')', true)
                // The close button stays in the tab order, after the modal content.
                ->assertScript('document.querySelector(\'[data-testid="escape-close-disabled-modal"] .fi-modal-close-btn\').tabIndex', 0)
                ->assertScript('Boolean(document.activeElement.compareDocumentPosition(document.querySelector(\'[data-testid="escape-close-disabled-modal"] .fi-modal-close-btn\')) & Node.DOCUMENT_POSITION_FOLLOWING)', true)
                // The relocated close button still closes the modal.
                ->click('[data-testid="escape-close-disabled-modal"] .fi-modal-close-btn')
                ->assertMissing('[data-testid="escape-close-disabled-modal"]')
                ->assertNoSmoke()
                ->assertNoAccessibilityIssues();

            visit('/modal-browser-test')
                ->inDarkMode()
                ->click('Escape close disabled')
                ->assertVisible('[data-testid="escape-close-disabled-modal"]')
                ->assertNoAccessibilityIssues();
        });
    });

    it('cancels parent actions when a nested modal using `cancelParentActionsOnClose()` is dismissed', function (): void {
        retry(10, function (): void {
            $this->actingAs(User::factory()->create());

            visit('/modal-browser-test')
                ->assertSee('Modal Browser Test')
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
                ->assertNoSmoke()
                ->assertNoAccessibilityIssues();

            visit('/modal-browser-test')
                ->inDarkMode()
                ->assertNoAccessibilityIssues();
        });
    });

    it('keeps the parent modal focus trap active while a nested modal is open', function (): void {
        retry(10, function (): void {
            $this->actingAs(User::factory()->create());

            visit('/modal-browser-test')
                ->assertSee('Modal Browser Test')
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

    it('locks and restores page scroll when opening and closing a single modal', function (): void {
        retry(10, function (): void {
            $this->actingAs(User::factory()->create());

            visit('/modal-browser-test')
                ->assertSee('Modal Browser Test')
                ->assertScript('document.documentElement.style.overflow', '')
                ->click('[data-testid="standalone-trigger"]')
                ->assertVisible('[data-testid="standalone-modal"]')
                ->assertScript('document.documentElement.style.overflow', 'hidden')
                ->click('[data-testid="standalone-close"]')
                ->assertMissing('[data-testid="standalone-modal"]')
                ->assertScript('document.documentElement.style.overflow', '')
                ->assertNoSmoke();
        });
    });

    it('lets clicks reach the page behind a click-through modal', function (): void {
        retry(10, function (): void {
            $this->actingAs(User::factory()->create());

            visit('/modal-browser-test')
                ->assertSee('Behind: not clicked')
                ->click('[data-testid="click-through-trigger"]')
                ->assertVisible('[data-testid="click-through-modal"]')
                // The open modal covers the page, but because it is click-through
                // the click passes through to the button behind it.
                ->click('[data-testid="behind-button"]')
                ->assertSee('Behind: clicked')
                ->assertNoSmoke();
        });
    });

    it('does not lock page scroll for a click-through modal', function (): void {
        retry(10, function (): void {
            $this->actingAs(User::factory()->create());

            visit('/modal-browser-test')
                ->assertSee('Modal Browser Test')
                ->assertScript('document.documentElement.style.overflow', '')
                ->click('[data-testid="click-through-trigger"]')
                ->assertVisible('[data-testid="click-through-modal"]')
                // A click-through modal lets you interact with the page behind it,
                // so it must leave the page scrollable.
                ->assertScript('document.documentElement.style.overflow', '')
                ->assertNoSmoke();
        });
    });

    it('releases the page scroll lock after a dismissed nested modal cancels all parent actions', function (): void {
        retry(10, function (): void {
            $this->actingAs(User::factory()->create());

            visit('/modal-browser-test')
                ->assertSee('Modal Browser Test')
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
                ->assertScript('document.documentElement.style.overflow', '')
                ->assertNoSmoke();
        });
    });

    it('restores focus after a nested overlay action cancels its parent action', function (): void {
        retry(10, function (): void {
            $this->actingAs(User::factory()->create());

            visit('/modal-browser-test')
                ->assertSee('Modal Browser Test')
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
