<?php

namespace Filament\Tests\Schemas;

use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

uses(TestCase::class);

beforeEach(function (): void {
    Artisan::call('filament:assets');
});

it('loads a deferred schema when it enters the viewport', function (): void {
    retry(10, function (): void {
        $this->actingAs(User::factory()->create());

        visit('/deferred-schema-loading-browser-test')
            ->assertPresent('[data-testid="viewport-deferred-section"] .fi-section-content.fi-sc-loading')
            ->assertMissing('#form\.deferredDetails\.deferred_name')
            ->assertNoAccessibilityIssues()
            ->assertScript("(() => { document.querySelector('[data-testid=\"viewport-deferred-section\"]').scrollIntoView(); return true })()", true)
            ->waitForText('Deferred name')
            ->assertPresent('#form\.deferredDetails\.deferred_name')
            ->assertPresent('[data-testid="viewport-deferred-section"] .fi-section-content')
            ->assertMissing('[data-testid="viewport-deferred-section"] .fi-sc-loading')
            ->assertNoSmoke();

        visit('/deferred-schema-loading-browser-test')
            ->inDarkMode()
            ->assertNoAccessibilityIssues();
    });
});

it('loads a concealed deferred schema after its parent is revealed', function (): void {
    retry(10, function (): void {
        $this->actingAs(User::factory()->create());

        visit('/deferred-schema-loading-browser-test')
            ->assertPresent('[data-testid="concealed-deferred-section"] .fi-sc-loading')
            ->assertMissing('#form\.concealedDeferredDetails\.concealed_deferred_name')
            ->click('[data-testid="concealed-deferred-section"] .fi-section-collapse-btn')
            ->waitForText('Concealed deferred name')
            ->assertPresent('#form\.concealedDeferredDetails\.concealed_deferred_name')
            ->assertMissing('[data-testid="concealed-deferred-section"] .fi-sc-loading')
            ->assertNoSmoke();
    });
});

it('loads a deferred schema when it contains a validation error', function (): void {
    retry(10, function (): void {
        $this->actingAs(User::factory()->create());

        visit('/deferred-schema-loading-browser-test')
            ->assertMissing('#form\.deferredDetails\.deferred_name')
            ->click('Validate')
            ->waitForText('The deferred name field is required.')
            ->assertPresent('#form\.deferredDetails\.deferred_name')
            ->assertMissing('[data-testid="viewport-deferred-section"] .fi-sc-loading')
            ->assertNoSmoke();
    });
});

it('loads deferred tab schemas when their tabs become active', function (): void {
    retry(10, function (): void {
        $this->actingAs(User::factory()->create());

        visit('/deferred-schema-loading-browser-test')
            ->assertMissing('#form\.deferredTabs\.profileTab\.profile_name')
            ->assertMissing('#form\.deferredTabs\.preferencesTab\.timezone')
            ->assertScript("(() => { document.querySelector('[data-testid=\"deferred-tabs\"]').scrollIntoView(); return true })()", true)
            ->waitForText('Deferred profile name')
            ->assertPresent('#form\.deferredTabs\.profileTab\.profile_name')
            ->assertMissing('#form\.deferredTabs\.preferencesTab\.timezone')
            ->click('Preferences')
            ->waitForText('Deferred timezone')
            ->assertPresent('#form\.deferredTabs\.preferencesTab\.timezone')
            ->assertNoAccessibilityIssues()
            ->assertNoSmoke();

        visit('/deferred-schema-loading-browser-test')
            ->inDarkMode()
            ->assertScript("(() => { document.querySelector('[data-testid=\"deferred-tabs\"]').scrollIntoView(); return true })()", true)
            ->waitForText('Deferred profile name')
            ->click('Preferences')
            ->waitForText('Deferred timezone')
            ->assertNoAccessibilityIssues();
    });
});

it('loads deferred wizard step schemas when their steps become active', function (): void {
    retry(10, function (): void {
        $this->actingAs(User::factory()->create());

        visit('/deferred-schema-loading-browser-test')
            ->assertMissing('#form\.deferredWizard\.accountStep\.account_name')
            ->assertMissing('#form\.deferredWizard\.confirmationStep\.confirmation_note')
            ->assertScript("(() => { document.querySelector('[data-testid=\"deferred-wizard\"]').scrollIntoView(); return true })()", true)
            ->waitForText('Deferred account name')
            ->assertPresent('#form\.deferredWizard\.accountStep\.account_name')
            ->assertMissing('#form\.deferredWizard\.confirmationStep\.confirmation_note')
            ->click('Next')
            ->waitForText('Deferred confirmation note')
            ->assertPresent('#form\.deferredWizard\.confirmationStep\.confirmation_note')
            ->assertNoAccessibilityIssues()
            ->assertNoSmoke();

        visit('/deferred-schema-loading-browser-test')
            ->inDarkMode()
            ->assertScript("(() => { document.querySelector('[data-testid=\"deferred-wizard\"]').scrollIntoView(); return true })()", true)
            ->waitForText('Deferred account name')
            ->click('Next')
            ->waitForText('Deferred confirmation note')
            ->assertNoAccessibilityIssues();
    });
});
