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
        ->assertScript("document.activeElement.matches('{$firstItem}')", true)
        ->assertAttribute($firstItem, 'role', 'menuitem')
        ->assertAttribute($firstItem, 'tabindex', '-1')
        ->keys($firstItem, 'ArrowDown')
        ->assertScript("document.activeElement.matches('{$disabledItem}')", true)
        ->assertAttribute($disabledItem, 'aria-disabled', 'true')
        ->assertScript("!document.activeElement.hasAttribute('disabled')", true)
        ->keys($disabledItem, 'Enter')
        ->assertVisible($lastItem)
        ->keys($disabledItem, 'ArrowDown')
        ->assertScript("document.activeElement.matches('{$lastItem}')", true)
        ->keys($lastItem, 'ArrowDown')
        ->assertScript("document.activeElement.matches('{$firstItem}')", true)
        ->keys($firstItem, 'Escape')
        ->assertMissing($firstItem)
        ->assertScript("document.activeElement.matches('{$trigger}')", true)
        ->click($trigger)
        ->assertVisible($firstItem)
        ->keys($trigger, 'Tab')
        ->assertMissing($firstItem)
        ->assertScript("document.activeElement.matches('[data-testid=\"refresh\"]')", true)
        ->click($trigger)
        ->assertVisible($firstItem)
        ->keys($trigger, 'Shift+Tab')
        ->assertMissing($firstItem)
        ->assertScript("document.activeElement.matches('[data-testid=\"dropdown-trigger\"]') || (/AppleWebKit/.test(navigator.userAgent) && !/Chrome/.test(navigator.userAgent) && !document.activeElement.closest('[role=\"menu\"]'))", true)
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
        ->assertScript("document.activeElement.matches('[data-testid=\"nested-menu-item\"]')", true)
        ->keys('[data-testid="nested-menu-item"]', 'Tab')
        ->assertMissing('[data-testid="nested-menu-item"]')
        ->assertVisible('.fi-select-input-btn')
        ->assertAttribute('[data-testid="dropdown-trigger"]', 'aria-expanded', 'true')
        ->assertScript("document.activeElement.matches('[data-testid=\"after-nested-menu\"]')", true)
        ->assertNoSmoke();
});

it('closes nested form controls before their dropdown on `Escape`', function (bool $isDarkMode): void {
    Artisan::call('filament:assets');

    $this->actingAs(User::factory()->create());

    $page = visit('/dropdown-test');

    if ($isDarkMode) {
        $page = $page->inDarkMode();
    }

    $page
        ->click('[data-testid="dropdown-trigger"]')
        ->click('.fi-select-input-btn')
        ->type('.fi-select-input-search-ctn input', 'Draft')
        ->assertVisible('.fi-select-input-search-ctn input')
        ->keys('.fi-select-input-search-ctn input', 'Escape')
        ->assertVisible('.fi-select-input-btn')
        ->assertAttribute('.fi-select-input-btn', 'aria-expanded', 'false')
        ->assertMissing('.fi-select-input-search-ctn input')
        ->assertPresent('.fi-select-input-btn:focus')
        ->keys('.fi-select-input-btn', 'Escape')
        ->assertMissing('.fi-select-input-btn')
        ->assertPresent('[data-testid="dropdown-trigger"]:focus')
        ->keys('[data-testid="dropdown-trigger"]', 'Enter')
        ->assertVisible('.fi-select-input-btn')
        ->click('.fi-select-input-btn')
        ->assertValue('.fi-select-input-search-ctn input', '')
        ->assertVisible('.fi-select-input-option[data-value="published"]')
        ->click('.fi-select-input-btn')
        ->assertMissing('.fi-select-input-search-ctn input')
        ->click('.fi-fo-date-time-picker-display-text-input')
        ->assertVisible('.fi-fo-date-time-picker-panel')
        ->keys('.fi-fo-date-time-picker-year-input', 'Escape')
        ->assertMissing('.fi-fo-date-time-picker-panel')
        ->assertVisible('.fi-fo-date-time-picker-display-text-input')
        ->assertAttribute('[data-testid="dropdown-trigger"]', 'aria-expanded', 'true')
        ->assertPresent('.fi-fo-date-time-picker-display-text-input:focus')
        ->keys('.fi-fo-date-time-picker-display-text-input', 'Escape')
        ->assertMissing('.fi-fo-date-time-picker-display-text-input')
        ->assertPresent('[data-testid="dropdown-trigger"]:focus')
        ->assertNoSmoke()
        ->assertNoAccessibilityIssues();
})->with(['light' => false, 'dark' => true]);

it('keeps `aria-expanded` in sync with the panel after a Livewire request', function (bool $isDarkMode): void {
    Artisan::call('filament:assets');

    $this->actingAs(User::factory()->create());

    $page = visit('/dropdown-test');

    if ($isDarkMode) {
        $page = $page->inDarkMode();
    }

    $page
        ->click('[data-testid="refresh"]')
        ->waitForText('Refreshed 1 times')
        ->click('[data-testid="dropdown-trigger"]')
        ->assertVisible('.fi-select-input-btn')
        ->assertAttribute('[data-testid="dropdown-trigger"]', 'aria-expanded', 'true')
        ->click('h1[class]')
        ->assertMissing('.fi-select-input-btn')
        ->assertAttribute('[data-testid="dropdown-trigger"]', 'aria-expanded', 'false')
        ->assertNoSmoke()
        ->assertNoAccessibilityIssues();
})->with(['light' => false, 'dark' => true]);

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

it('closes a rich editor toolbar menu before its dropdown on `Escape`', function (bool $isDarkMode): void {
    Artisan::call('filament:assets');

    $this->actingAs(User::factory()->create());

    $page = visit('/dropdown-test');

    if ($isDarkMode) {
        $page = $page->inDarkMode();
    }

    $trigger = '.fi-fo-rich-editor-dropdown-tool-trigger';
    $menu = '.fi-fo-rich-editor-dropdown-tool-menu';

    $page
        ->click('[data-testid="dropdown-trigger"]')
        ->click($trigger)
        ->assertVisible($menu)
        ->keys($trigger, 'Escape')
        ->assertMissing($menu)
        ->assertVisible($trigger)
        ->assertScript("document.activeElement.matches('{$trigger}')", true)
        ->assertAttribute('[data-testid="dropdown-trigger"]', 'aria-expanded', 'true')
        ->keys($trigger, 'Escape')
        ->assertMissing($trigger)
        ->assertAttribute('[data-testid="dropdown-trigger"]', 'aria-expanded', 'false')
        ->assertNoSmoke()
        ->assertNoAccessibilityIssues();
})->with(['light' => false, 'dark' => true]);
