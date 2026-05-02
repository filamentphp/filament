<?php

use Filament\Support\Colors\Color;
use Filament\Support\View\Components\ColorMaps\ComponentColorMap;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('finds the lightest shade meeting `$minRatio` against `$surface`', function (): void {
    $map = ComponentColorMap::make(Color::Red)
        ->slot('text', surface: Color::Red[50])
        ->get();

    expect($map['text'])->toBeInt()->toBeGreaterThanOrEqual(500);
});

it('walks darkest-first when `$shouldStartFromDarkest` is `true`', function (): void {
    $ascending = ComponentColorMap::make(Color::Red)
        ->slot('text', surface: Color::Gray[700])
        ->get();

    $descending = ComponentColorMap::make(Color::Red)
        ->slot('text', surface: Color::Gray[700], shouldStartFromDarkest: true)
        ->get();

    expect($descending['text'])->toBeGreaterThan($ascending['text']);
});

it('respects `$maxShade` when searching', function (): void {
    $map = ComponentColorMap::make(Color::Red)
        ->slot('text', surface: Color::Gray[700], maxShade: 400, shouldStartFromDarkest: true)
        ->get();

    expect($map['text'])->toBeLessThanOrEqual(400);
});

it('respects `$minShade` when searching', function (): void {
    $map = ComponentColorMap::make(Color::Red)
        ->slot('text', surface: 'oklch(1 0 0)', minShade: 600)
        ->get();

    expect($map['text'])->toBeGreaterThanOrEqual(600);
});

it('falls back to `$fallback` when no shade qualifies', function (): void {
    // White against itself never passes any contrast threshold.
    $whitePalette = array_fill_keys([50, 100, 200, 300, 400, 500, 600, 700, 800, 900, 950], 'oklch(1 0 0)');

    $map = ComponentColorMap::make($whitePalette)
        ->slot('text', surface: 'oklch(1 0 0)', fallback: 950)
        ->get();

    expect($map['text'])->toBe(950);
});

it('uses a custom `$minRatio` to require stronger contrast', function (): void {
    $relaxed = ComponentColorMap::make(Color::Red)
        ->slot('text', surface: Color::Red[50], minRatio: Color::WCAG_AA_TEXT)
        ->get();

    $strict = ComponentColorMap::make(Color::Red)
        ->slot('text', surface: Color::Red[50], minRatio: Color::WCAG_AAA_TEXT)
        ->get();

    expect($strict['text'])->toBeGreaterThanOrEqual($relaxed['text']);
});
