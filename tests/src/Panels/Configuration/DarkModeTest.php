<?php

use Filament\Panel;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('enables dark mode, the forced state, and the toggle by default', function (): void {
    $panel = Panel::make();

    expect($panel->hasDarkMode())->toBeTrue();
    expect($panel->hasDarkModeForced())->toBeFalse();
    expect($panel->hasDarkModeToggle())->toBeTrue();
});

it('can disable dark mode using `darkMode()`', function (): void {
    $panel = Panel::make()->darkMode(false);

    expect($panel->hasDarkMode())->toBeFalse();
});

it('can force dark mode using `darkMode()`', function (): void {
    $panel = Panel::make()->darkMode(isForced: true);

    expect($panel->hasDarkMode())->toBeTrue();
    expect($panel->hasDarkModeForced())->toBeTrue();
});

it('can disable the dark mode toggle using `darkModeToggle()`', function (): void {
    $panel = Panel::make()->darkModeToggle(false);

    expect($panel->hasDarkMode())->toBeTrue();
    expect($panel->hasDarkModeToggle())->toBeFalse();
});

it('can use a `Closure` to control dark mode', function (): void {
    $panel = Panel::make()->darkMode(fn (): bool => false);

    expect($panel->hasDarkMode())->toBeFalse();
});

it('can use a `Closure` to control the forced state of dark mode', function (): void {
    $panel = Panel::make()->darkMode(isForced: fn (): bool => true);

    expect($panel->hasDarkModeForced())->toBeTrue();
});

it('can use a `Closure` to control the dark mode toggle', function (): void {
    $panel = Panel::make()->darkModeToggle(fn (): bool => false);

    expect($panel->hasDarkModeToggle())->toBeFalse();
});
