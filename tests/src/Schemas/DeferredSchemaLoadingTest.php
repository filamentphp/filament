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

    $page = visit('/deferred-schema-loading-browser-test', ['reducedMotion' => 'reduce']);

    $page->assertPresent('[data-testid="viewport-deferred-section"] [role="status"][aria-busy="true"]')
        ->assertNotPresent('#form\.deferredDetails\.deferred_name')
        ->assertNotPresent('#form\.deferredTabs\.profileTab\.profile_name')
        ->assertNotPresent('#form\.deferredTabs\.preferencesTab\.timezone')
        ->assertNotPresent('#form\.deferredWizard\.accountStep\.account_name')
        ->assertNotPresent('#form\.deferredWizard\.confirmationStep\.confirmation_note')
        ->assertPresent('[data-testid="concealed-deferred-section"] [aria-busy="true"]')
        ->assertNotPresent('#form\.concealedDeferredDetails\.concealed_deferred_name')
        ->assertNoAccessibilityIssues();

    $page->page()->locator('[data-testid="concealed-deferred-section"] [aria-expanded]')->click();
    $page->assertVisible('#form\.concealedDeferredDetails\.concealed_deferred_name')
        ->assertMissing('[data-testid="concealed-deferred-section"] [aria-busy="true"]');

    $page->page()->locator('[data-testid="viewport-deferred-section"]')->scrollIntoViewIfNeeded();
    $page->assertVisible('#form\.deferredDetails\.deferred_name')
        ->assertMissing('[data-testid="viewport-deferred-section"] [aria-busy="true"]');

    $page->page()->locator('[data-testid="deferred-tabs"]')->scrollIntoViewIfNeeded();
    $page->assertVisible('#form\.deferredTabs\.profileTab\.profile_name')
        ->assertNotPresent('#form\.deferredTabs\.preferencesTab\.timezone')
        ->click('[data-testid="deferred-tabs"] [role="tab"] >> text=Preferences')
        ->assertVisible('#form\.deferredTabs\.preferencesTab\.timezone');

    $page->page()->locator('[data-testid="deferred-wizard"]')->scrollIntoViewIfNeeded();
    $page->assertVisible('#form\.deferredWizard\.accountStep\.account_name')
        ->assertNotPresent('#form\.deferredWizard\.confirmationStep\.confirmation_note')
        ->click('Next')
        ->assertVisible('#form\.deferredWizard\.confirmationStep\.confirmation_note')
        ->assertNoSmoke()
        ->assertNoAccessibilityIssues();

    $darkPage = $page->inDarkMode();
    $darkPage->assertPresent('[data-testid="viewport-deferred-section"] [aria-busy="true"]')
        ->assertNoAccessibilityIssues();
    $darkPage->page()->locator('[data-testid="concealed-deferred-section"] [aria-expanded]')->click();
    $darkPage->assertVisible('#form\.concealedDeferredDetails\.concealed_deferred_name');
    $darkPage->page()->locator('[data-testid="viewport-deferred-section"]')->scrollIntoViewIfNeeded();
    $darkPage->assertVisible('#form\.deferredDetails\.deferred_name');
    $darkPage->page()->locator('[data-testid="deferred-tabs"]')->scrollIntoViewIfNeeded();
    $darkPage->click('[data-testid="deferred-tabs"] [role="tab"] >> text=Preferences')
        ->assertVisible('#form\.deferredTabs\.preferencesTab\.timezone');
    $darkPage->page()->locator('[data-testid="deferred-wizard"]')->scrollIntoViewIfNeeded();
    $darkPage->assertVisible('#form\.deferredWizard\.accountStep\.account_name')
        ->click('Next')
        ->assertVisible('#form\.deferredWizard\.confirmationStep\.confirmation_note')
        ->assertNoAccessibilityIssues();
});

it('loads a deferred schema when it contains a validation error', function (): void {
    $this->actingAs(User::factory()->create());

    visit('/deferred-schema-loading-browser-test')
        ->assertNotPresent('#form\.deferredDetails\.deferred_name')
        ->click('Validate')
        ->assertVisible('#form\.deferredDetails\.deferred_name')
        ->assertVisible('[data-testid="viewport-deferred-section"] [data-validation-error]')
        ->assertMissing('[data-testid="viewport-deferred-section"] [aria-busy="true"]')
        ->assertNoSmoke();
});
