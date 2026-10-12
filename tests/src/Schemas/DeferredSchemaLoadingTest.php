<?php

namespace Filament\Tests\Schemas;

use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

uses(TestCase::class);

beforeEach(function (): void {
    Artisan::call('filament:assets');
});

it('loads deferred sections, tabs, and wizard steps as they are revealed', function (): void {
    $this->actingAs(User::factory()->create());

    foreach ([false, true] as $isDarkMode) {
        $page = visit('/deferred-schema-loading-browser-test', ['reducedMotion' => 'reduce']);

        if ($isDarkMode) {
            $page = $page->inDarkMode();
        }

        $page->assertPresent('[data-testid="viewport-deferred-section"] [role="status"][aria-busy="true"]')
            ->assertMissing('#form\.deferredDetails\.deferred_name')
            ->assertMissing('#form\.deferredTabs\.profileTab\.profile_name')
            ->assertMissing('#form\.deferredTabs\.preferencesTab\.timezone')
            ->assertMissing('#form\.deferredWizard\.accountStep\.account_name')
            ->assertMissing('#form\.deferredWizard\.confirmationStep\.confirmation_note')
            ->assertNoAccessibilityIssues();

        $page->page()->locator('[data-testid="viewport-deferred-section"]')->scrollIntoViewIfNeeded();
        $page->assertVisible('#form\.deferredDetails\.deferred_name')
            ->assertMissing('[data-testid="viewport-deferred-section"] [aria-busy="true"]')
            ->assertPresent('[data-testid="concealed-deferred-section"] [aria-busy="true"]')
            ->assertMissing('#form\.concealedDeferredDetails\.concealed_deferred_name')
            ->click('[data-testid="concealed-deferred-section"] [aria-expanded="false"]')
            ->assertVisible('#form\.concealedDeferredDetails\.concealed_deferred_name')
            ->assertMissing('[data-testid="concealed-deferred-section"] [aria-busy="true"]');

        $page->page()->locator('[data-testid="deferred-tabs"]')->scrollIntoViewIfNeeded();
        $page->assertVisible('#form\.deferredTabs\.profileTab\.profile_name')
            ->assertMissing('#form\.deferredTabs\.preferencesTab\.timezone')
            ->click('[data-testid="deferred-tabs"] [role="tab"] >> text=Preferences')
            ->assertVisible('#form\.deferredTabs\.preferencesTab\.timezone');

        $page->page()->locator('[data-testid="deferred-wizard"]')->scrollIntoViewIfNeeded();
        $page->assertVisible('#form\.deferredWizard\.accountStep\.account_name')
            ->assertMissing('#form\.deferredWizard\.confirmationStep\.confirmation_note')
            ->click('Next')
            ->assertVisible('#form\.deferredWizard\.confirmationStep\.confirmation_note')
            ->assertNoSmoke()
            ->assertNoAccessibilityIssues();
    }
});

it('loads a deferred schema when it contains a validation error', function (): void {
    $this->actingAs(User::factory()->create());

    visit('/deferred-schema-loading-browser-test')
        ->assertMissing('#form\.deferredDetails\.deferred_name')
        ->click('Validate')
        ->assertVisible('#form\.deferredDetails\.deferred_name')
        ->assertVisible('[data-testid="viewport-deferred-section"] [data-validation-error]')
        ->assertMissing('[data-testid="viewport-deferred-section"] [aria-busy="true"]')
        ->assertNoSmoke();
});
