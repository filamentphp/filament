<?php

use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

uses(TestCase::class);

beforeEach(function (): void {
    Artisan::call('filament:assets');
});

it('can toggle state by clicking in the browser', function (): void {
    $this->actingAs(User::factory()->create());

    visit('/toggle-test')
        ->assertSee('Toggle Test')
        ->assertSee('Basic Toggle')
        ->assertAttribute('[data-testid="toggle"]', 'aria-checked', 'false')
        ->click('[data-testid="toggle"]')
        ->assertAttribute('[data-testid="toggle"]', 'aria-checked', 'true')
        ->click('[data-testid="toggle"]')
        ->assertAttribute('[data-testid="toggle"]', 'aria-checked', 'false')
        ->assertNoSmoke()
        ->assertNoAccessibilityIssues();

    visit('/toggle-test')
        ->inDarkMode()
        ->assertNoAccessibilityIssues();
});
