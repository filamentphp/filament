<?php

use Filament\Facades\Filament;
use Filament\Panel;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('enables dark mode, the theme switcher, and disables the forced state by default', function (): void {
    $panel = Panel::make();

    expect($panel->hasDarkMode())->toBeTrue();
    expect($panel->isDarkModeForced())->toBeFalse();
    expect($panel->hasThemeSwitcher())->toBeTrue();
});

describe('`darkMode()`', function (): void {
    it('can disable dark mode', function (): void {
        $panel = Panel::make()->darkMode(false);

        expect($panel->hasDarkMode())->toBeFalse();
    });

    it('can force dark mode using `darkMode()`', function (): void {
        $panel = Panel::make()->darkMode(isForced: true);

        expect($panel->hasDarkMode())->toBeTrue();
        expect($panel->isDarkModeForced())->toBeTrue();
    });

    it('can use a `Closure` to control dark mode', function (): void {
        $panel = Panel::make()->darkMode(fn (): bool => false);

        expect($panel->hasDarkMode())->toBeFalse();
    });

    it('can use a `Closure` to control the forced state of dark mode', function (): void {
        $panel = Panel::make()->darkMode(isForced: fn (): bool => true);

        expect($panel->isDarkModeForced())->toBeTrue();
    });
});

it('reads the forced state independently of availability through either getter', function (string $method): void {
    $panel = Filament::getCurrentOrDefaultPanel();
    $isForced = true;
    $panel->darkMode(condition: false, isForced: static function () use (&$isForced): bool {
        return $isForced;
    });

    expect($panel->{$method}())->toBeTrue()
        ->and(filament()->{$method}())->toBeTrue()
        ->and(Filament::{$method}())->toBeTrue()
        ->and(Filament::hasDarkMode())->toBeFalse();

    $isForced = false;

    expect($panel->{$method}())->toBeFalse()
        ->and(filament()->{$method}())->toBeFalse()
        ->and(Filament::{$method}())->toBeFalse();

    Filament::setCurrentPanel(Panel::make()->darkMode(isForced: true));

    expect(Filament::{$method}())->toBeTrue()
        ->and(Filament::hasDarkMode())->toBeTrue();
})->with(['isDarkModeForced', 'hasDarkModeForced']);

describe('`themeSwitcher()`', function (): void {
    it('can hide the theme switcher without disabling dark mode', function (): void {
        $panel = Panel::make()->themeSwitcher(false);

        expect($panel->hasDarkMode())->toBeTrue();
        expect($panel->hasThemeSwitcher())->toBeFalse();
    });

    it('can use a `Closure` to control the theme switcher', function (): void {
        $panel = Panel::make()->themeSwitcher(fn (): bool => false);

        expect($panel->hasThemeSwitcher())->toBeFalse();
    });
});
