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
    retry(10, function (): void {
        $this->actingAs(User::factory()->create());

        $finiteAnimationsHaveFinished = 'document.getAnimations().every((animation) => animation.effect.getTiming().iterations === Infinity || animation.playState === "finished")';

        visit('/deferred-schema-loading-browser-test')
            ->assertPresent('[data-testid="viewport-deferred-section"] .fi-section-content.fi-sc-loading')
            ->assertMissing('#form\.deferredDetails\.deferred_name')
            ->assertMissing('#form\.deferredTabs\.profileTab\.profile_name')
            ->assertMissing('#form\.deferredTabs\.preferencesTab\.timezone')
            ->assertMissing('#form\.deferredWizard\.accountStep\.account_name')
            ->assertMissing('#form\.deferredWizard\.confirmationStep\.confirmation_note')
            ->assertScript($finiteAnimationsHaveFinished)
            ->assertNoAccessibilityIssues()
            ->assertScript("(() => { document.querySelector('[data-testid=\"viewport-deferred-section\"]').scrollIntoView(); return true })()", true)
            ->assertVisible('#form\.deferredDetails\.deferred_name')
            ->assertMissing('[data-testid="viewport-deferred-section"] .fi-sc-loading')
            ->assertPresent('[data-testid="concealed-deferred-section"] .fi-sc-loading')
            ->assertMissing('#form\.concealedDeferredDetails\.concealed_deferred_name')
            ->click('[data-testid="concealed-deferred-section"] .fi-section-collapse-btn')
            ->assertVisible('#form\.concealedDeferredDetails\.concealed_deferred_name')
            ->assertMissing('[data-testid="concealed-deferred-section"] .fi-sc-loading')
            ->assertScript("(() => { document.querySelector('[data-testid=\"deferred-tabs\"]').scrollIntoView(); return true })()", true)
            ->assertVisible('#form\.deferredTabs\.profileTab\.profile_name')
            ->assertMissing('#form\.deferredTabs\.preferencesTab\.timezone')
            ->click('Preferences')
            ->assertVisible('#form\.deferredTabs\.preferencesTab\.timezone')
            ->assertScript("(() => { document.querySelector('[data-testid=\"deferred-wizard\"]').scrollIntoView(); return true })()", true)
            ->assertVisible('#form\.deferredWizard\.accountStep\.account_name')
            ->assertMissing('#form\.deferredWizard\.confirmationStep\.confirmation_note')
            ->click('Next')
            ->assertVisible('#form\.deferredWizard\.confirmationStep\.confirmation_note')
            ->assertNoSmoke();

        visit('/deferred-schema-loading-browser-test')
            ->inDarkMode()
            ->assertScript($finiteAnimationsHaveFinished)
            ->assertNoAccessibilityIssues()
            ->assertScript("(() => { document.querySelector('[data-testid=\"deferred-tabs\"]').scrollIntoView(); return true })()", true)
            ->assertVisible('#form\.deferredTabs\.profileTab\.profile_name')
            ->click('Preferences')
            ->assertVisible('#form\.deferredTabs\.preferencesTab\.timezone')
            ->assertScript("(() => { document.querySelector('[data-testid=\"deferred-wizard\"]').scrollIntoView(); return true })()", true)
            ->assertVisible('#form\.deferredWizard\.accountStep\.account_name')
            ->click('Next')
            ->assertVisible('#form\.deferredWizard\.confirmationStep\.confirmation_note')
            ->assertScript($finiteAnimationsHaveFinished)
            ->assertNoAccessibilityIssues();
    });
});

it('loads a deferred schema when it contains a validation error', function (): void {
    retry(10, function (): void {
        $this->actingAs(User::factory()->create());

        visit('/deferred-schema-loading-browser-test')
            ->assertMissing('#form\.deferredDetails\.deferred_name')
            ->click('Validate')
            ->assertVisible('#form\.deferredDetails\.deferred_name')
            ->assertVisible('[data-testid="viewport-deferred-section"] [data-validation-error]')
            ->assertMissing('[data-testid="viewport-deferred-section"] .fi-sc-loading')
            ->assertNoSmoke();
    });
});
