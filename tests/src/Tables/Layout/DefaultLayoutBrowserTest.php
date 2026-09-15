<?php

use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Tables\TestCase;
use Illuminate\Support\Facades\Artisan;

uses(TestCase::class);

it('renders the default layout and sidebar filters without accessibility issues', function (): void {
    retry(10, function (): void {
        Artisan::call('filament:assets');

        $this->actingAs(User::factory()->create());

        Post::factory()->count(3)->create();

        visit('/default-table-layout-browser-test')
            ->assertPresent('#default-table .fi-ta-header-toolbar')
            ->assertPresent('#default-table .fi-ta-table')
            ->assertPresent('#sidebar-filters-table .fi-ta-filters-before-content-ctn')
            ->assertNoSmoke()
            ->assertNoAccessibilityIssues();

        visit('/default-table-layout-browser-test')
            ->inDarkMode()
            ->assertNoAccessibilityIssues();
    });
});

it('toggles the sidebar filters popover on small screens', function (): void {
    retry(10, function (): void {
        Artisan::call('filament:assets');

        $this->actingAs(User::factory()->create());

        Post::factory()->count(3)->create();

        visit('/default-table-layout-browser-test')
            ->resize(390, 844)
            ->assertScript('document.querySelector(\'#sidebar-filters-table .fi-ta-filters-before-content-ctn\').classList.contains(\'fi-open\')', false)
            ->click('#sidebar-filters-table .fi-ta-filters-trigger-action-ctn button')
            ->wait(1)
            ->assertScript('document.querySelector(\'#sidebar-filters-table .fi-ta-filters-before-content-ctn\').classList.contains(\'fi-open\')', true)
            ->assertVisible('#sidebar-filters-table .fi-ta-filters-before-content')
            ->click('#default-table .fi-ta-header-toolbar')
            ->wait(1)
            ->assertScript('document.querySelector(\'#sidebar-filters-table .fi-ta-filters-before-content-ctn\').classList.contains(\'fi-open\')', false)
            ->assertNoSmoke()
            ->assertNoAccessibilityIssues();

        visit('/default-table-layout-browser-test')
            ->inDarkMode()
            ->resize(390, 844)
            ->click('#sidebar-filters-table .fi-ta-filters-trigger-action-ctn button')
            ->wait(1)
            ->assertNoAccessibilityIssues();
    });
});
