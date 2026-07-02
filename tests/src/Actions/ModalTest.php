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
