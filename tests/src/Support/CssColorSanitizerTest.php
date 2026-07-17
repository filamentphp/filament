<?php

use Filament\Tests\TestCase;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;

uses(TestCase::class);

it('returns `null` for `null` input', function (): void {
    expect(Str::sanitizeCssColor(null))->toBeNull();
});

it('returns `null` for an empty string', function (): void {
    expect(Str::sanitizeCssColor(''))->toBeNull();
});

it('returns `null` for a whitespace-only string', function (): void {
    expect(Str::sanitizeCssColor('   '))->toBeNull();
});

it('allows `#rgb` hex colors', function (): void {
    expect(Str::sanitizeCssColor('#f00'))->toBe('#f00');
});

it('allows `#rgba` hex colors', function (): void {
    expect(Str::sanitizeCssColor('#f00c'))->toBe('#f00c');
});

it('allows `#rrggbb` hex colors', function (): void {
    expect(Str::sanitizeCssColor('#ff0000'))->toBe('#ff0000');
});

it('allows `#rrggbbaa` hex colors', function (): void {
    expect(Str::sanitizeCssColor('#ff0000cc'))->toBe('#ff0000cc');
});

it('allows mixed-case hex colors', function (): void {
    expect(Str::sanitizeCssColor('#AbCdEf'))->toBe('#AbCdEf');
});

it('allows bare CSS keyword colors', function (): void {
    expect(Str::sanitizeCssColor('red'))->toBe('red')
        ->and(Str::sanitizeCssColor('transparent'))->toBe('transparent')
        ->and(Str::sanitizeCssColor('rebeccapurple'))->toBe('rebeccapurple');
});

it('allows `rgb()` and `rgba()` functional colors', function (): void {
    expect(Str::sanitizeCssColor('rgb(255, 0, 0)'))->toBe('rgb(255, 0, 0)')
        ->and(Str::sanitizeCssColor('rgba(255, 0, 0, 0.5)'))->toBe('rgba(255, 0, 0, 0.5)');
});

it('allows `hsl()` and `hsla()` functional colors', function (): void {
    expect(Str::sanitizeCssColor('hsl(120, 50%, 50%)'))->toBe('hsl(120, 50%, 50%)')
        ->and(Str::sanitizeCssColor('hsla(120, 50%, 50%, 0.5)'))->toBe('hsla(120, 50%, 50%, 0.5)');
});

it('allows `hwb()`, `lab()`, `lch()`, `oklab()`, `oklch()` and `color()` functional colors', function (): void {
    expect(Str::sanitizeCssColor('hwb(194 0% 0%)'))->toBe('hwb(194 0% 0%)')
        ->and(Str::sanitizeCssColor('lab(52.2% 40.1 59.9)'))->toBe('lab(52.2% 40.1 59.9)')
        ->and(Str::sanitizeCssColor('lch(52.2% 72.2 50)'))->toBe('lch(52.2% 72.2 50)')
        ->and(Str::sanitizeCssColor('oklab(0.4 0.1 0.1)'))->toBe('oklab(0.4 0.1 0.1)')
        ->and(Str::sanitizeCssColor('oklch(0.7 0.15 30)'))->toBe('oklch(0.7 0.15 30)')
        ->and(Str::sanitizeCssColor('color(display-p3 1 0 0)'))->toBe('color(display-p3 1 0 0)');
});

it('trims surrounding whitespace from valid colors', function (): void {
    expect(Str::sanitizeCssColor('  #ff0000  '))->toBe('#ff0000');
});

it('rejects values containing a `;` that would break out of the declaration', function (): void {
    expect(Str::sanitizeCssColor('red;position:fixed;inset:0;background-image:url(//attacker)'))
        ->toBeNull();
});

it('rejects functional notation containing `url()`', function (): void {
    expect(Str::sanitizeCssColor('rgb(0,0,0);background-image:url(//attacker)'))->toBeNull();
});

it('rejects functional notation containing a nested `(` or `)`', function (): void {
    expect(Str::sanitizeCssColor('rgb(calc(1))'))->toBeNull();
});

it('rejects functional notation containing a `:`', function (): void {
    expect(Str::sanitizeCssColor('rgb(0:0:0)'))->toBeNull();
});

it('rejects functional notation containing quotes', function (): void {
    expect(Str::sanitizeCssColor('rgb("0",0,0)'))->toBeNull()
        ->and(Str::sanitizeCssColor("rgb('0',0,0)"))->toBeNull();
});

it('rejects an unknown function name', function (): void {
    expect(Str::sanitizeCssColor('expression(alert(1))'))->toBeNull();
});

it('rejects a hex color with an invalid length', function (): void {
    expect(Str::sanitizeCssColor('#ff'))->toBeNull()
        ->and(Str::sanitizeCssColor('#fffff'))->toBeNull();
});

it('rejects a keyword containing non-alphabetic characters', function (): void {
    expect(Str::sanitizeCssColor('red;'))->toBeNull()
        ->and(Str::sanitizeCssColor('red 0'))->toBeNull();
});

it('allows a string-backed enum, using its value', function (): void {
    expect(Str::sanitizeCssColor(CssColorStringBackedEnum::Hex))->toBe('#ff0000')
        ->and(Str::sanitizeCssColor(CssColorStringBackedEnum::Keyword))->toBe('red')
        ->and(Str::sanitizeCssColor(CssColorStringBackedEnum::Untrimmed))->toBe('#00ff00');
});

it('rejects a string-backed enum with an unsafe value', function (): void {
    expect(Str::sanitizeCssColor(CssColorStringBackedEnum::Unsafe))->toBeNull();
});

it('rejects an int-backed enum, since its value is not a valid color', function (): void {
    expect(Str::sanitizeCssColor(CssColorIntBackedEnum::Red))->toBeNull();
});

it('exposes a `Stringable` macro', function (): void {
    expect(Str::of('#ff0000')->sanitizeCssColor())
        ->toBeInstanceOf(Stringable::class)
        ->and((string) Str::of('#ff0000')->sanitizeCssColor())
        ->toBe('#ff0000')
        ->and((string) Str::of('red;position:fixed')->sanitizeCssColor())
        ->toBe('');
});

enum CssColorStringBackedEnum: string
{
    case Hex = '#ff0000';
    case Keyword = 'red';
    case Untrimmed = '  #00ff00  ';
    case Unsafe = 'red;position:fixed';
}

enum CssColorIntBackedEnum: int
{
    case Red = 0xFF0000;
}
