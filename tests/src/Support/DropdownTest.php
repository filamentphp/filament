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
