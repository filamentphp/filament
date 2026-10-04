<?php

use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

uses(TestCase::class);

it('allows a searchable select inside a dropdown to clean up on `Escape`', function (bool $isDarkMode): void {
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
        ->assertMissing('.fi-select-input-btn')
        ->keys('[data-testid="dropdown-trigger"]', 'Enter')
        ->assertVisible('.fi-select-input-btn')
        ->assertAttribute('.fi-select-input-btn', 'aria-expanded', 'false')
        ->assertMissing('.fi-select-input-search-ctn input')
        ->click('.fi-select-input-btn')
        ->assertValue('.fi-select-input-search-ctn input', '')
        ->assertVisible('text=Published')
        ->click('.fi-select-input-btn')
        ->assertMissing('.fi-select-input-search-ctn input')
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
