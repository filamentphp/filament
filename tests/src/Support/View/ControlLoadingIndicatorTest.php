<?php

use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

uses(TestCase::class);

it('swaps the icon of a control for its loading indicator while its request runs', function (string $control): void {
    Artisan::call('filament:assets');

    $this->actingAs(User::factory()->create());

    $icon = "[data-testid=\"{$control}\"] > svg";
    $loadingIndicator = "[data-testid=\"{$control}\"] > .fi-loading-indicator";

    visit('/control-loading-indicator-browser-test')
        ->assertVisible($icon)
        ->assertMissing($loadingIndicator)
        ->click("[data-testid=\"{$control}\"]")
        ->assertVisible($loadingIndicator)
        ->assertMissing($icon)
        ->assertVisible($icon)
        ->assertMissing($loadingIndicator)
        ->assertNoSmoke();
})->with([
    'action button' => ['slow-button'],
    'action icon button' => ['slow-icon-button'],
    'Blade button' => ['blade-button'],
    'Blade dropdown item' => ['blade-dropdown-item'],
]);

it('renders controls with loading indicators accessibly', function (): void {
    Artisan::call('filament:assets');

    $this->actingAs(User::factory()->create());

    visit('/control-loading-indicator-browser-test')
        ->assertNoAccessibilityIssues();

    visit('/control-loading-indicator-browser-test')
        ->inDarkMode()
        ->assertNoAccessibilityIssues();
});
