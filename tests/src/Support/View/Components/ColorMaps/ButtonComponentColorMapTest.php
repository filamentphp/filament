<?php

use Filament\Support\Colors\Color;
use Filament\Support\View\Components\ColorMaps\ButtonComponentColorMap;
use Filament\Tests\TestCase;

uses(TestCase::class);

/**
 * @param  array<int, string>  $palette
 */
function defaultButtonColorMap(array $palette): ButtonComponentColorMap
{
    return ButtonComponentColorMap::make($palette)
        ->lightBackground(bg: 600, hover: 500)
        ->lightBackground(bg: 400, hover: 300, alternateHover: 500)
        ->darkBackground(bg: 600, hover: 500, alternateHover: 700);
}

it('produces all eight slots when fully configured for a vibrant palette', function (): void {
    $map = defaultButtonColorMap(Color::Red)->get();

    expect($map)
        ->toHaveKeys(['bg', 'hover:bg', 'dark:bg', 'dark:hover:bg', 'text', 'hover:text', 'dark:text', 'dark:hover:text']);
});

it('throws when no `lightBackground()` candidate is configured', function (): void {
    ButtonComponentColorMap::make(Color::Red)
        ->darkBackground(bg: 600, hover: 500, alternateHover: 700)
        ->get();
})->throws(LogicException::class, '`lightBackground()`');

it('throws when no `darkBackground()` candidate is configured', function (): void {
    ButtonComponentColorMap::make(Color::Red)
        ->lightBackground(bg: 600, hover: 500)
        ->get();
})->throws(LogicException::class, '`darkBackground()`');

it('uses `lightBackground()` when both shades resolve to light text', function (): void {
    $map = ButtonComponentColorMap::make(Color::Red)
        ->lightBackground(bg: 800, hover: 700)
        ->darkBackground(bg: 600, hover: 500, alternateHover: 700)
        ->get();

    expect($map)
        ->toMatchArray([
            'bg' => 800,
            'hover:bg' => 700,
        ]);
});

it('iterates multiple `lightBackground()` candidates in the order they were added', function (): void {
    $map = ButtonComponentColorMap::make(Color::Red)
        ->lightBackground(bg: 700, hover: 600)
        ->lightBackground(bg: 600, hover: 500)
        ->darkBackground(bg: 600, hover: 500, alternateHover: 700)
        ->get();

    // Red 700/600 both have light text, so the first candidate wins.
    expect($map)
        ->toMatchArray([
            'bg' => 700,
            'hover:bg' => 600,
        ]);
});

it('falls through to a later `lightBackground()` candidate when the first does not qualify', function (): void {
    // Yellow 600/500 take dark text (the first candidate fails the lightness check),
    // but Yellow 800/700 take light text — so the second candidate wins.
    $map = ButtonComponentColorMap::make(Color::Yellow)
        ->lightBackground(bg: 600, hover: 500)
        ->lightBackground(bg: 800, hover: 700)
        ->darkBackground(bg: 600, hover: 500, alternateHover: 700)
        ->get();

    expect($map)
        ->toMatchArray([
            'bg' => 800,
            'hover:bg' => 700,
        ]);
});

it('uses the last `lightBackground()` candidate as the fallback when no candidate produces light text on its bg', function (): void {
    // Yellow is pale enough that shade 600 takes dark text, so the first candidate
    // (600, 500) is rejected and the resolver falls through to the last candidate
    // (400, 300, 500), which is used unconditionally as the fallback.
    $map = defaultButtonColorMap(Color::Yellow)->get();

    expect($map['bg'])->toBe(400);
});

it('uses the last `lightBackground()` candidate as the fallback when only one candidate is configured', function (): void {
    $map = ButtonComponentColorMap::make(Color::Yellow)
        ->lightBackground(bg: 300, hover: 200, alternateHover: 400)
        ->darkBackground(bg: 600, hover: 500, alternateHover: 700)
        ->get();

    expect($map['bg'])->toBe(300);
});

it('iterates multiple `darkBackground()` candidates in the order they were added', function (): void {
    $map = ButtonComponentColorMap::make(Color::Red)
        ->lightBackground(bg: 600, hover: 500)
        ->darkBackground(bg: 700, hover: 600)
        ->darkBackground(bg: 600, hover: 500, alternateHover: 700)
        ->get();

    expect($map)
        ->toMatchArray([
            'dark:bg' => 700,
            'dark:hover:bg' => 600,
        ]);
});

it('uses the last `darkBackground()` candidate as the fallback when no candidate produces light text on its bg', function (): void {
    // For yellow, neither dark candidate has light text on its bg, so the last is used as fallback.
    $map = ButtonComponentColorMap::make(Color::Yellow)
        ->lightBackground(bg: 400, hover: 300, alternateHover: 500)
        ->darkBackground(bg: 600, hover: 500, alternateHover: 700)
        ->darkBackground(bg: 400, hover: 300, alternateHover: 500)
        ->get();

    expect($map['dark:bg'])->toBe(400);
});

it('falls back to white text for every slot when `minContrastRatio()` is unreachable', function (): void {
    $map = defaultButtonColorMap(Color::Red)
        ->minContrastRatio(50.0) // never satisfiable: forces every bg → white text
        ->get();

    expect($map['text'])->toBe(0)
        ->and($map['hover:text'])->toBe(0)
        ->and($map['dark:text'])->toBe(0)
        ->and($map['dark:hover:text'])->toBe(0);
});
