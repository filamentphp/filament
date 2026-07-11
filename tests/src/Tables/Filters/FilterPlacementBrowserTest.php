<?php

use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Tables\TestCase;
use Illuminate\Support\Facades\Artisan;

uses(TestCase::class);

it('renders filters split across placements in the browser without accessibility issues', function (): void {
    retry(10, function (): void {
        Artisan::call('filament:assets');

        $this->actingAs(User::factory()->create());

        Post::factory()->count(3)->create();

        visit('/filter-placement-browser-test')
            ->assertPresent('.fi-ta-filters-above-content-ctn')
            ->assertPresent('button[title="Filter"]')
            ->assertNoSmoke()
            ->assertNoAccessibilityIssues();

        visit('/filter-placement-browser-test')
            ->inDarkMode()
            ->assertNoAccessibilityIssues();
    });
})->group('browser');
