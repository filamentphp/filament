<?php

use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Tables\TestCase;
use Illuminate\Support\Facades\Artisan;

uses(TestCase::class);

it('renders filter panels in the browser with dialog triggers and no accessibility issues', function (): void {
    retry(10, function (): void {
        Artisan::call('filament:assets');

        $this->actingAs(User::factory()->create());

        Post::factory()->count(3)->create();

        visit('/filter-panel-browser-test')
            // The AboveContent panel renders above the table, the BelowContent panel below it...
            ->assertPresent('.fi-ta-filters-above-content-ctn')
            ->assertPresent('.fi-ta-filters-below-content')
            // ...and the Dropdown panel renders as a toolbar trigger.
            ->assertPresent('button[title="Filter"]')
            ->assertNoSmoke()
            ->assertNoAccessibilityIssues();

        visit('/filter-panel-browser-test')
            ->inDarkMode()
            ->assertNoAccessibilityIssues();
    });
})->group('browser');
