<?php

use Filament\Support\Colors\Color;
use Filament\Support\Colors\ColorManager;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('includes default colors from `getColors()`', function (): void {
    $manager = new ColorManager;

    $colors = $manager->getColors();

    expect($colors)
        ->toHaveKey('danger')
        ->toHaveKey('gray')
        ->toHaveKey('info')
        ->toHaveKey('primary')
        ->toHaveKey('success')
        ->toHaveKey('warning');
});

it('can register a custom color array via `register()`', function (): void {
    $manager = new ColorManager;
    $manager->register(['brand' => Color::Blue]);

    $colors = $manager->getColors();

    expect($colors)->toHaveKey('brand');
    expect($colors['brand'])->toBe(Color::Blue);
});

it('can register colors via a `Closure` using `register()`', function (): void {
    $manager = new ColorManager;
    $manager->register(static fn (): array => ['accent' => Color::Pink]);

    $colors = $manager->getColors();

    expect($colors)->toHaveKey('accent');
});

it('can register a custom color as a HEX string via `register()`', function (): void {
    $manager = new ColorManager;
    $manager->register(['custom' => '#3b82f6']);

    $colors = $manager->getColors();

    expect($colors)->toHaveKey('custom');
    // Palette should have standard shades
    expect($colors['custom'])->toHaveKey(50)->toHaveKey(500)->toHaveKey(950);
});

it('returns the correct color array via `getColor()`', function (): void {
    $manager = new ColorManager;

    $color = $manager->getColor('danger');

    expect($color)->toBe(Color::Red);
});

it('returns `null` via `getColor()` for unknown color names', function (): void {
    $manager = new ColorManager;

    expect($manager->getColor('nonexistent'))->toBeNull();
});

it('caches colors after first call to `getColors()`', function (): void {
    $manager = new ColorManager;

    $first = $manager->getColors();
    $second = $manager->getColors();

    expect($first)->toBe($second);
});

it('can store and retrieve overriding shades via `overrideShades()` and `getOverridingShades()`', function (): void {
    $manager = new ColorManager;
    $manager->overrideShades('primary', [100, 200, 300]);

    expect($manager->getOverridingShades('primary'))->toBe([100, 200, 300]);
});

it('returns `null` from `getOverridingShades()` when no overriding shades are set', function (): void {
    $manager = new ColorManager;

    expect($manager->getOverridingShades('primary'))->toBeNull();
});

it('can store and retrieve added shades via `addShades()` and `getAddedShades()`', function (): void {
    $manager = new ColorManager;
    $manager->addShades('primary', [25, 75]);

    expect($manager->getAddedShades('primary'))->toBe([25, 75]);
});

it('returns `null` from `getAddedShades()` when no added shades are set', function (): void {
    $manager = new ColorManager;

    expect($manager->getAddedShades('primary'))->toBeNull();
});

it('can store and retrieve removed shades via `removeShades()` and `getRemovedShades()`', function (): void {
    $manager = new ColorManager;
    $manager->removeShades('primary', [50, 100]);

    expect($manager->getRemovedShades('primary'))->toBe([50, 100]);
});

it('returns `null` from `getRemovedShades()` when no removed shades are set', function (): void {
    $manager = new ColorManager;

    expect($manager->getRemovedShades('primary'))->toBeNull();
});

it('`register()` returns the same `ColorManager` instance for fluent chaining', function (): void {
    $manager = new ColorManager;

    expect($manager->register(['foo' => Color::Green]))->toBe($manager);
});
