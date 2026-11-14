<?php

namespace Filament\Tests\Schemas;

use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

uses(TestCase::class);

beforeEach(function (): void {
    Artisan::call('filament:assets');
});

it('loads lazy-loaded sections, tabs, and wizard steps as they are revealed', function (): void {
    $this->actingAs(User::factory()->create());

    $page = visit('/lazy-schema-loading-browser-test', ['reducedMotion' => 'reduce']);

    $page->assertPresent('[data-testid="viewport-lazy-loaded-section"] [role="status"][aria-busy="true"]')
        ->assertNotPresent('#form\.lazyLoadedDetails\.lazy_loaded_name')
        ->assertNotPresent('#form\.lazyLoadedTabs\.profileTab\.profile_name')
        ->assertNotPresent('#form\.lazyLoadedTabs\.preferencesTab\.timezone')
        ->assertNotPresent('#form\.lazyLoadedWizard\.accountStep\.account_name')
        ->assertNotPresent('#form\.lazyLoadedWizard\.confirmationStep\.confirmation_note')
        ->assertPresent('[data-testid="concealed-lazy-loaded-section"] [aria-busy="true"]')
        ->assertNotPresent('#form\.concealedLazyLoadedDetails\.concealed_lazy_loaded_name')
        ->assertNoAccessibilityIssues();

    $page->page()->locator('[data-testid="concealed-lazy-loaded-section"] [aria-expanded]')->click();
    $page->assertVisible('#form\.concealedLazyLoadedDetails\.concealed_lazy_loaded_name')
        ->assertMissing('[data-testid="concealed-lazy-loaded-section"] [aria-busy="true"]');

    $page->page()->locator('[data-testid="viewport-lazy-loaded-section"]')->scrollIntoViewIfNeeded();
    $page->assertVisible('#form\.lazyLoadedDetails\.lazy_loaded_name')
        ->assertMissing('[data-testid="viewport-lazy-loaded-section"] [aria-busy="true"]');

    $page->page()->locator('[data-testid="lazy-loaded-tabs"]')->scrollIntoViewIfNeeded();
    $page->assertVisible('#form\.lazyLoadedTabs\.profileTab\.profile_name')
        ->assertNotPresent('#form\.lazyLoadedTabs\.preferencesTab\.timezone')
        ->click('[data-testid="lazy-loaded-tabs"] [role="tab"] >> text=Preferences')
        ->assertVisible('#form\.lazyLoadedTabs\.preferencesTab\.timezone');

    $page->page()->locator('[data-testid="lazy-loaded-wizard"]')->scrollIntoViewIfNeeded();
    $page->assertVisible('#form\.lazyLoadedWizard\.accountStep\.account_name')
        ->assertNotPresent('#form\.lazyLoadedWizard\.confirmationStep\.confirmation_note')
        ->click('Next')
        ->assertVisible('#form\.lazyLoadedWizard\.confirmationStep\.confirmation_note')
        ->assertNoSmoke()
        ->assertNoAccessibilityIssues();

    $darkPage = $page->inDarkMode();
    $darkPage->assertPresent('[data-testid="viewport-lazy-loaded-section"] [aria-busy="true"]')
        ->assertNoAccessibilityIssues();
    $darkPage->page()->locator('[data-testid="concealed-lazy-loaded-section"] [aria-expanded]')->click();
    $darkPage->assertVisible('#form\.concealedLazyLoadedDetails\.concealed_lazy_loaded_name');
    $darkPage->page()->locator('[data-testid="viewport-lazy-loaded-section"]')->scrollIntoViewIfNeeded();
    $darkPage->assertVisible('#form\.lazyLoadedDetails\.lazy_loaded_name');
    $darkPage->page()->locator('[data-testid="lazy-loaded-tabs"]')->scrollIntoViewIfNeeded();
    $darkPage->click('[data-testid="lazy-loaded-tabs"] [role="tab"] >> text=Preferences')
        ->assertVisible('#form\.lazyLoadedTabs\.preferencesTab\.timezone');
    $darkPage->page()->locator('[data-testid="lazy-loaded-wizard"]')->scrollIntoViewIfNeeded();
    $darkPage->assertVisible('#form\.lazyLoadedWizard\.accountStep\.account_name')
        ->click('Next')
        ->assertVisible('#form\.lazyLoadedWizard\.confirmationStep\.confirmation_note')
        ->assertNoAccessibilityIssues();
});

it('shows an accessible inline error when a lazy-loaded schema fails', function (): void {
    $this->actingAs(User::factory()->create());

    $page = visit('/lazy-schema-loading-browser-test?shouldFailLoading=1');

    $page->page()->locator('[data-testid="concealed-lazy-loaded-section"] [aria-expanded]')->click();
    $page->assertVisible('[data-testid="concealed-lazy-loaded-section"] [role="alert"][aria-busy="false"]')
        ->assertNotPresent('#form\\.concealedLazyLoadedDetails\\.concealed_lazy_loaded_name')
        ->assertMissing('dialog[open]')
        ->assertNoAccessibilityIssues();

    $darkPage = $page->inDarkMode();
    $darkPage->page()->locator('[data-testid="concealed-lazy-loaded-section"] [aria-expanded]')->click();
    $darkPage->assertVisible('[data-testid="concealed-lazy-loaded-section"] [role="alert"][aria-busy="false"]')
        ->assertNoAccessibilityIssues();
});

it('loads a lazy-loaded schema when it contains a validation error', function (): void {
    $this->actingAs(User::factory()->create());

    visit('/lazy-schema-loading-browser-test')
        ->assertNotPresent('#form\.lazyLoadedDetails\.lazy_loaded_name')
        ->click('Validate')
        ->assertVisible('#form\.lazyLoadedDetails\.lazy_loaded_name')
        ->assertVisible('[data-testid="viewport-lazy-loaded-section"] [data-validation-error]')
        ->assertMissing('[data-testid="viewport-lazy-loaded-section"] [aria-busy="true"]')
        ->assertNoSmoke();
});
