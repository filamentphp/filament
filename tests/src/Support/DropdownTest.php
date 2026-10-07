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
        ->assertScript('(window.originalCheckVisibility = Element.prototype.checkVisibility, Element.prototype.checkVisibility = undefined, true)', true)
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
        ->assertScript("document.querySelector('[data-testid=\"dropdown-trigger\"]').getAttribute('aria-controls') === document.querySelector('[data-testid=\"dropdown-trigger\"]').closest('.fi-dropdown').querySelector('.fi-dropdown-panel').id", true)
        ->assertScript("!document.querySelector('[data-testid=\"dropdown-trigger\"]').closest('.fi-dropdown').querySelector('.fi-dropdown-panel').hasAttribute('role')", true)
        ->assertScript('(Element.prototype.checkVisibility = window.originalCheckVisibility, true)', true)
        ->assertNoSmoke();
});

it('keeps an opted-out popup open when closing a nested menu', function (): void {
    Artisan::call('filament:assets');

    $this->actingAs(User::factory()->create());

    visit('/dropdown-test')
        ->click('[data-testid="dropdown-trigger"]')
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
        ->assertScript("document.activeElement.matches('.fi-select-input-btn')", true)
        ->keys('.fi-select-input-btn', 'Escape')
        ->assertMissing('.fi-select-input-btn')
        ->assertScript("document.activeElement.matches('[data-testid=\"dropdown-trigger\"]')", true)
        ->keys('[data-testid="dropdown-trigger"]', 'Enter')
        ->assertVisible('.fi-select-input-btn')
        ->click('.fi-select-input-btn')
        ->assertValue('.fi-select-input-search-ctn input', '')
        ->assertVisible('.fi-select-input-option[data-value="published"]')
        ->click('.fi-select-input-btn')
        ->assertMissing('.fi-select-input-search-ctn input')
        ->click('.fi-fo-date-time-picker-display-text-input')
        ->assertVisible('.fi-fo-date-time-picker-panel')
        ->assertScript("(document.querySelector('.fi-fo-date-time-picker-year-input').focus(), document.activeElement.matches('.fi-fo-date-time-picker-year-input'))", true)
        ->keys('.fi-fo-date-time-picker-year-input', 'Escape')
        ->assertMissing('.fi-fo-date-time-picker-panel')
        ->assertVisible('.fi-fo-date-time-picker-display-text-input')
        ->assertAttribute('[data-testid="dropdown-trigger"]', 'aria-expanded', 'true')
        ->assertScript("document.activeElement.matches('.fi-fo-date-time-picker-display-text-input')", true)
        ->keys('.fi-fo-date-time-picker-display-text-input', 'Escape')
        ->assertMissing('.fi-fo-date-time-picker-display-text-input')
        ->assertScript("document.activeElement.matches('[data-testid=\"dropdown-trigger\"]')", true)
        ->assertNoSmoke()
        ->assertScript('document.querySelector(\'[data-testid="dropdown-trigger"]\').closest(\'.fi-dropdown\').getAnimations({ subtree: true }).length', 0)
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

    $page->script('window.enclosingEscapeCount = 0; window.addEventListener(\'keydown\', (event) => { if (event.key === \'Escape\') window.enclosingEscapeCount++ })');

    $page
        ->click('[data-testid="dropdown-trigger"]')
        ->assertVisible('.fi-select-input-btn')
        ->assertScript("(Alpine.\$data(document.querySelector('[data-testid=\"secondary-dropdown-container\"]')).isSecondaryDropdownShown = true, true)", true)
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
        ->assertScript("document.activeElement.matches('[data-testid=\"secondary-dropdown-trigger\"]')", true)
        ->assertMissing('[data-testid="secondary-dropdown-content"]')
        ->assertVisible('.fi-select-input-btn')
        ->keys('[data-testid="secondary-dropdown-trigger"]', 'Escape')
        ->assertMissing('.fi-select-input-btn')
        ->assertScript("document.activeElement.matches('[data-testid=\"dropdown-trigger\"]')", true)
        ->assertScript('window.enclosingEscapeCount', 0);
});

it('closes nested dropdowns when their parent closes', function (): void {
    Artisan::call('filament:assets');

    $this->actingAs(User::factory()->create());

    visit('/dropdown-test')
        ->click('[data-testid="dropdown-trigger"]')
        ->assertScript("(Alpine.\$data(document.querySelector('[data-testid=\"secondary-dropdown-container\"]')).isSecondaryDropdownShown = true, true)", true)
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
        ->assertScript("(document.dispatchEvent(new Event('livewire:navigate')), true)", true)
        ->assertMissing('.fi-select-input-btn')
        ->keys('[data-testid="dropdown-trigger"]', 'Enter')
        ->assertVisible('.fi-select-input-btn')
        ->assertMissing('[data-testid="secondary-dropdown-content"]')
        ->assertMissing('[data-testid="tertiary-dropdown-content"]')
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
