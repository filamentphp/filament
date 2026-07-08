<?php

use Filament\Panel;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('enables dark mode, the theme switcher, and disables the forced state by default', function (): void {
    $panel = Panel::make();

    expect($panel->hasDarkMode())->toBeTrue();
    expect($panel->hasDarkModeForced())->toBeFalse();
    expect($panel->hasThemeSwitcher())->toBeTrue();
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

it('can hide the theme switcher using `themeSwitcher()`', function (): void {
    $panel = Panel::make()->themeSwitcher(false);

    expect($panel->hasDarkMode())->toBeTrue();
    expect($panel->hasThemeSwitcher())->toBeFalse();
});

it('can use a `Closure` to control dark mode', function (): void {
    $panel = Panel::make()->darkMode(fn (): bool => false);

    expect($panel->hasDarkMode())->toBeFalse();
});

it('can use a `Closure` to control the forced state of dark mode', function (): void {
    $panel = Panel::make()->darkMode(isForced: fn (): bool => true);

    expect($panel->hasDarkModeForced())->toBeTrue();
});

it('can use a `Closure` to control the theme switcher', function (): void {
    $panel = Panel::make()->themeSwitcher(fn (): bool => false);

    expect($panel->hasThemeSwitcher())->toBeFalse();
});
