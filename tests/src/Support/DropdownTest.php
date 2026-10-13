<?php

use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

uses(TestCase::class);

it('uses menu behavior by default and supports opting out', function (): void {
    Artisan::call('filament:assets');

    $this->actingAs(User::factory()->create());

    $trigger = '[data-testid="menu-dropdown-trigger"]';
    $firstItem = '[data-testid="menu-dropdown-first-item"]';
    $disabledItem = '[data-testid="menu-dropdown-disabled-item"]';
    $lastItem = '[data-testid="menu-dropdown-last-item"]';

    visit('/dropdown-test')
        ->assertAttribute($trigger, 'aria-haspopup', 'menu')
        ->assertAttribute('[data-testid="dropdown-trigger"]', 'aria-haspopup', 'true')
        ->keys($trigger, 'Enter')
        ->assertPresent($firstItem . ':focus')
        ->assertAttribute($firstItem, 'role', 'menuitem')
        ->assertAttribute($firstItem, 'tabindex', '-1')
        ->keys($firstItem, 'ArrowDown')
        ->assertPresent($disabledItem . ':focus')
        ->assertAttribute($disabledItem, 'aria-disabled', 'true')
        ->assertAttributeMissing($disabledItem, 'disabled')
        ->keys($disabledItem, 'Enter')
        ->assertVisible($lastItem)
        ->keys($disabledItem, 'ArrowDown')
        ->assertPresent($lastItem . ':focus')
        ->keys($lastItem, 'ArrowDown')
        ->assertPresent($firstItem . ':focus')
        ->keys($firstItem, 'Escape')
        ->assertMissing($firstItem)
        ->assertPresent($trigger . ':focus')
        ->click($trigger)
        ->assertVisible($firstItem)
        ->keys($trigger, 'Tab')
        ->assertMissing($firstItem)
        ->assertPresent('[data-testid="refresh"]:focus')
        ->click($trigger)
        ->assertVisible($firstItem)
        ->keys($trigger, 'Shift+Tab')
        ->assertMissing($firstItem)
        ->assertPresent('[data-testid="dropdown-trigger"]:focus')
        ->click('[data-testid="dropdown-trigger"]')
        ->assertScript("document.getElementById(document.querySelector('[data-testid=\"dropdown-trigger\"]').getAttribute('aria-controls')).contains(document.querySelector('[data-testid=\"outer-dropdown-action\"]'))", true)
        ->assertScript("!document.getElementById(document.querySelector('[data-testid=\"dropdown-trigger\"]').getAttribute('aria-controls')).hasAttribute('role')", true)
        ->assertNoSmoke();
});

it('keeps an opted-out popup open when closing a nested menu', function (): void {
    Artisan::call('filament:assets');

    $this->actingAs(User::factory()->create());

    visit('/dropdown-test')
        ->click('[data-testid="dropdown-trigger"]')
        ->assertVisible('[data-testid="nested-menu-trigger"]')
        ->keys('[data-testid="nested-menu-trigger"]', 'Enter')
        ->assertPresent('[data-testid="nested-menu-item"]:focus')
        ->keys('[data-testid="nested-menu-item"]', 'Tab')
        ->assertMissing('[data-testid="nested-menu-item"]')
        ->assertVisible('button[aria-haspopup="listbox"]')
        ->assertAttribute('[data-testid="dropdown-trigger"]', 'aria-expanded', 'true')
        ->assertPresent('[data-testid="after-nested-menu"]:focus')
        ->assertNoSmoke();
});

it('closes nested form controls before their dropdown on `Escape`', function (): void {
    Artisan::call('filament:assets');

    $this->actingAs(User::factory()->create());

    $page = visit('/dropdown-test');

    $select = 'button[aria-haspopup="listbox"]';
    $search = 'input[role="combobox"]:not([aria-haspopup="dialog"])';
    $date = 'input[aria-haspopup="dialog"]';

    $page
        ->click('[data-testid="dropdown-trigger"]')
        ->click($select)
        ->type($search, 'Draft')
        ->assertVisible($search)
        ->assertNoAccessibilityIssues()
        ->keys($search, 'Escape')
        ->assertVisible($select)
        ->assertAttribute($select, 'aria-expanded', 'false')
        ->assertMissing($search)
        ->assertPresent($select . ':focus')
        ->keys($select, 'Escape')
        ->assertMissing($select)
        ->assertPresent('[data-testid="dropdown-trigger"]:focus')
        ->keys('[data-testid="dropdown-trigger"]', 'Enter')
        ->assertVisible($select)
        ->click($select)
        ->assertValue($search, '')
        ->assertVisible('[role="option"][data-value="published"]')
        ->click($select)
        ->assertMissing($search)
        ->click($date)
        ->assertVisible('[role="dialog"]')
        ->assertNoAccessibilityIssues()
        ->keys('[role="dialog"] input[type="number"]', 'Escape')
        ->assertMissing('[role="dialog"]')
        ->assertVisible($date)
        ->assertAttribute('[data-testid="dropdown-trigger"]', 'aria-expanded', 'true')
        ->assertPresent($date . ':focus')
        ->keys($date, 'Escape')
        ->assertMissing($date)
        ->assertPresent('[data-testid="dropdown-trigger"]:focus')
        ->assertNoSmoke();

    $page->inDarkMode()
        ->click('[data-testid="dropdown-trigger"]')
        ->click($select)
        ->assertVisible($search)
        ->assertNoAccessibilityIssues()
        ->keys($search, 'Escape')
        ->click($date)
        ->assertVisible('[role="dialog"]')
        ->assertNoAccessibilityIssues();
});

it('keeps `aria-expanded` in sync with the panel after a Livewire request', function (): void {
    Artisan::call('filament:assets');

    $this->actingAs(User::factory()->create());

    $page = visit('/dropdown-test');

    $page
        ->click('[data-testid="refresh"]')
        ->assertSee('Refreshed 1 times')
        ->click('[data-testid="dropdown-trigger"]')
        ->assertVisible('.fi-select-input-btn')
        ->assertAttribute('[data-testid="dropdown-trigger"]', 'aria-expanded', 'true')
        ->click('nav[aria-label="Topbar"]')
        ->assertMissing('.fi-select-input-btn')
        ->assertAttribute('[data-testid="dropdown-trigger"]', 'aria-expanded', 'false')
        ->assertNoSmoke();
});

it('only closes the owning dynamically mounted dropdown on `Escape`', function (): void {
    Artisan::call('filament:assets');

    $this->actingAs(User::factory()->create());

    $page = visit('/dropdown-test');

    $page
        ->click('[data-testid="dropdown-trigger"]')
        ->assertVisible('.fi-select-input-btn')
        ->click('[data-testid="show-secondary-dropdown"]')
        ->assertVisible('[data-testid="secondary-dropdown-trigger"]')
        ->assertVisible('.fi-select-input-btn')
        ->keys('[data-testid="secondary-dropdown-trigger"]', 'Enter')
        ->assertVisible('[data-testid="secondary-dropdown-content"]')
        ->assertVisible('.fi-select-input-btn')
        ->hover('[data-testid="secondary-dropdown-content-button"]')
        ->assertVisible('[data-tippy-root]')
        ->keys('[data-testid="secondary-dropdown-content-button"]', 'Escape')
        ->assertMissing('[data-tippy-root]')
        ->assertVisible('[data-testid="secondary-dropdown-content"]')
        ->assertVisible('.fi-select-input-btn')
        ->keys('[data-testid="secondary-dropdown-content-button"]', 'Escape')
        ->assertPresent('[data-testid="secondary-dropdown-trigger"]:focus')
        ->assertMissing('[data-testid="secondary-dropdown-content"]')
        ->assertVisible('.fi-select-input-btn')
        ->keys('[data-testid="secondary-dropdown-trigger"]', 'Escape')
        ->assertMissing('.fi-select-input-btn')
        ->assertPresent('[data-testid="dropdown-trigger"]:focus');
});

it('closes nested dropdowns when their parent closes', function (): void {
    Artisan::call('filament:assets');

    $this->actingAs(User::factory()->create());

    visit('/dropdown-test')
        ->click('[data-testid="dropdown-trigger"]')
        ->click('[data-testid="show-secondary-dropdown"]')
        ->assertVisible('[data-testid="secondary-dropdown-trigger"]')
        ->keys('[data-testid="secondary-dropdown-trigger"]', 'Enter')
        ->assertVisible('[data-testid="secondary-dropdown-content"]')
        ->keys('[data-testid="secondary-dropdown-content-button"]', 'Enter')
        ->assertVisible('[data-testid="tertiary-dropdown-content"]')
        ->click('[data-testid="outer-dropdown-action"]')
        ->keys('[data-testid="outer-dropdown-action"]', 'Escape')
        ->assertAttribute('[data-testid="dropdown-trigger"]', 'aria-expanded', 'false')
        ->assertMissing('.fi-select-input-btn')
        ->keys('[data-testid="dropdown-trigger"]', 'Enter')
        ->assertVisible('.fi-select-input-btn')
        ->assertMissing('[data-testid="secondary-dropdown-content"]')
        ->assertMissing('[data-testid="tertiary-dropdown-content"]')
        ->keys('[data-testid="secondary-dropdown-trigger"]', 'Enter')
        ->assertVisible('[data-testid="secondary-dropdown-content"]')
        ->keys('[data-testid="secondary-dropdown-content-button"]', 'Enter')
        ->assertVisible('[data-testid="tertiary-dropdown-content"]')
        ->assertNoSmoke();
});

it('closes a rich editor toolbar menu before its dropdown on `Escape`', function (): void {
    Artisan::call('filament:assets');

    $this->actingAs(User::factory()->create());

    $page = visit('/dropdown-test');

    $trigger = '[data-rich-editor-tool="Headings"]';
    $menu = '[role="menu"]:visible';

    $page
        ->click('[data-testid="dropdown-trigger"]')
        ->click($trigger)
        ->assertVisible($menu)
        ->assertNoAccessibilityIssues()
        ->keys($trigger, 'Escape')
        ->assertMissing($menu)
        ->assertVisible($trigger)
        ->assertPresent($trigger . ':focus')
        ->assertAttribute('[data-testid="dropdown-trigger"]', 'aria-expanded', 'true')
        ->keys($trigger, 'Escape')
        ->assertMissing($trigger)
        ->assertAttribute('[data-testid="dropdown-trigger"]', 'aria-expanded', 'false')
        ->assertNoSmoke();

    $page->inDarkMode()
        ->click('[data-testid="dropdown-trigger"]')
        ->click($trigger)
        ->assertVisible($menu)
        ->assertNoAccessibilityIssues();
});
