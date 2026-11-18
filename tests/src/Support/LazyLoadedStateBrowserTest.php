<?php

namespace Filament\Tests\Support;

use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

uses(TestCase::class);

beforeEach(function (): void {
    Artisan::call('filament:assets');
    $this->actingAs(User::factory()->create());
});

it('loads independent values, whole columns, rows, and entries then refreshes with the parent', function (): void {
    $page = visit('/lazy-loaded-state-browser-test');

    $page->assertSee('Independent entry v1')
        ->assertSee('Total: 0')
        ->assertSee('No result')
        ->assertSee('Individual Alpha v1')
        ->assertSee('Individual Beta v1')
        ->assertSee('Column Alpha / 2')
        ->assertSee('Column Beta / 2')
        ->assertSee('First Alpha')
        ->assertSee('Second Alpha')
        ->assertSee('First Beta')
        ->assertSee('Second Beta')
        ->assertDontSee('Private entry')
        ->assertDontSee('Private column')
        ->assertNoSmoke()
        ->assertNoAccessibilityIssues();

    $page->click('Refresh state')
        ->assertSee('Independent entry v2')
        ->assertSee('Individual Alpha v2')
        ->assertSee('Individual Beta v2')
        ->assertSee('Column Beta / 2')
        ->assertNoSmoke();

    $page->inDarkMode()
        ->assertSee('Independent entry v1')
        ->assertSee('Column Beta / 2')
        ->assertNoAccessibilityIssues();
});

it('waits for intersection while loading all rendered members of an explicit group together', function (): void {
    $page = visit('/lazy-loaded-state-browser-test?viewport=1');

    $page->assertSee('Viewport controls are ready')
        ->click('Toggle values')
        ->assertMissing('[data-testid="viewport-values"]')
        ->click('Toggle values')
        ->assertPresent('[data-testid="viewport-values"] [aria-busy="true"]')
        ->click('[data-testid="viewport-values"] [aria-expanded]')
        ->assertSee('First independent value')
        ->assertSee('First grouped value / 2')
        ->assertSeeIn('[data-testid="last-grouped-value"]', 'Last grouped value / 2')
        ->assertDontSee('Last independent value');

    $page->page()->locator('[data-testid="last-independent-value"]')->scrollIntoViewIfNeeded();
    $page->assertSee('Last independent value')
        ->assertNoSmoke()
        ->assertNoAccessibilityIssues();

    $darkPage = $page->inDarkMode();
    $darkPage->click('[data-testid="viewport-values"] [aria-expanded]')
        ->assertSee('First grouped value / 2');
    $darkPage->page()->locator('[data-testid="last-independent-value"]')->scrollIntoViewIfNeeded();
    $darkPage->assertSee('Last independent value')
        ->assertNoAccessibilityIssues();
});

it('loads nested lazy parents and supports their child confirmation actions', function (bool $lazyItemSchemas): void {
    $page = visit('/lazy-loaded-repeatable-entry-browser-test' . ($lazyItemSchemas ? '?lazyItemSchemas=1' : ''));

    $page->assertSee('Parcel 1048 / box v1')
        ->assertSee('Parcel 1056 / box v1')
        ->assertSee('Two deliveries')
        ->assertNoSmoke()
        ->assertNoAccessibilityIssues();

    $page->click('Select Parcel 1056')
        ->assertVisible('[role="alertdialog"] button[type="submit"]')
        ->assertNoAccessibilityIssues()
        ->click('Confirm')
        ->assertSee('Parcel 1056')
        ->assertDontSee('No parcel selected')
        ->assertSee('Parcel 1048 / box v1')
        ->click('Refresh parcels')
        ->assertSee('Parcel 1048 / box v2')
        ->assertSee('Parcel 1056 / box v2')
        ->assertNoSmoke();

    $darkPage = $page->inDarkMode();
    $darkPage->assertSee('Parcel 1056 / box v1')
        ->assertNoAccessibilityIssues()
        ->click('Select Parcel 1048')
        ->assertVisible('[role="alertdialog"] button[type="submit"]')
        ->assertNoAccessibilityIssues();
})->with(['eager item schemas' => false, 'lazy item schemas' => true]);
