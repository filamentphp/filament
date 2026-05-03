<?php

use Filament\Support\Colors\Color;
use Filament\Support\View\Components\ColorMaps\IconButtonComponentColorMap;
use Filament\Tests\TestCase;

uses(TestCase::class);

/**
 * @param  array<int, string>  $palette
 */
function defaultIconButtonColorMap(array $palette): IconButtonComponentColorMap
{
    return IconButtonComponentColorMap::make($palette)
        ->lightSurface(Color::Gray[50])
        ->darkSurface(Color::Gray[700]);
}

it('produces text/hover/dark slots when fully configured', function (): void {
    $map = defaultIconButtonColorMap(Color::Red)->get();

    expect($map)->toHaveKeys(['text', 'hover:text', 'dark:text', 'dark:hover:text']);
});

it('throws when no `lightSurface()` is configured', function (): void {
    IconButtonComponentColorMap::make(Color::Red)
        ->darkSurface(Color::Gray[700])
        ->get();
})->throws(LogicException::class, '`lightSurface()`');

it('throws when no `darkSurface()` is configured', function (): void {
    IconButtonComponentColorMap::make(Color::Red)
        ->lightSurface(Color::Gray[50])
        ->get();
})->throws(LogicException::class, '`darkSurface()`');

it('caps dark search at `darkMaxShade()`', function (): void {
    $map = defaultIconButtonColorMap(Color::Red)
        ->darkMaxShade(300)
        ->get();

    expect($map['dark:text'])->toBeLessThanOrEqual(300);
});

it('shifts the matched shade when `lightSurface()` is changed', function (): void {
    $defaultMap = defaultIconButtonColorMap(Color::Red)->get();

    $darkerLightSurfaceMap = IconButtonComponentColorMap::make(Color::Red)
        ->lightSurface(Color::Gray[300])
        ->darkSurface(Color::Gray[700])
        ->get();

    expect($darkerLightSurfaceMap)->not->toBe($defaultMap);
});

it('shifts the matched shade when `darkSurface()` is changed', function (): void {
    $defaultMap = defaultIconButtonColorMap(Color::Red)->get();

    $lighterDarkSurfaceMap = IconButtonComponentColorMap::make(Color::Red)
        ->lightSurface(Color::Gray[50])
        ->darkSurface(Color::Gray[500])
        ->get();

    expect($lighterDarkSurfaceMap)->not->toBe($defaultMap);
});

it('picks more contrasty shades when `minContrastRatio()` is bumped to AAA', function (): void {
    $relaxedMap = defaultIconButtonColorMap(Color::Red)
        ->minContrastRatio(Color::WCAG_AA_NON_TEXT)
        ->get();

    $strictMap = defaultIconButtonColorMap(Color::Red)
        ->minContrastRatio(Color::WCAG_AAA_TEXT)
        ->get();

    expect($strictMap)->not->toBe($relaxedMap);
});
