<?php

use Filament\Schemas\Components\StateCasts\EnumStateCast;
use Filament\Tests\Fixtures\Enums\IntegerBackedEnum;
use Filament\Tests\Fixtures\Enums\StringBackedEnum;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('can get an enum from a string', function (string $string, StringBackedEnum $enum): void {
    $cast = app(EnumStateCast::class, ['enum' => StringBackedEnum::class]);

    expect($cast->get($string))
        ->toBe($enum);
})->with([
    ['one', StringBackedEnum::One],
    ['two', StringBackedEnum::Two],
    ['three', StringBackedEnum::Three],
]);

it('can get an enum from an integer', function (int $integer, IntegerBackedEnum $enum): void {
    $cast = app(EnumStateCast::class, ['enum' => IntegerBackedEnum::class]);

    expect($cast->get($integer))
        ->toBe($enum);
})->with([
    [0, IntegerBackedEnum::Zero],
    [1, IntegerBackedEnum::One],
    [2, IntegerBackedEnum::Two],
    [3, IntegerBackedEnum::Three],
]);

it('can get an integer-backed enum from a numeric string', function (): void {
    $cast = app(EnumStateCast::class, ['enum' => IntegerBackedEnum::class]);

    expect($cast->get('0'))->toBe(IntegerBackedEnum::Zero)
        ->and($cast->get('2'))->toBe(IntegerBackedEnum::Two);
});

it('can ignore if an enum is passed to the getter already', function (StringBackedEnum $enum): void {
    $cast = app(EnumStateCast::class, ['enum' => StringBackedEnum::class]);

    expect($cast->get($enum))
        ->toBe($enum);
})->with([
    StringBackedEnum::One,
    StringBackedEnum::Two,
    StringBackedEnum::Three,
]);

it('can get an enum from a `Stringable` value', function (): void {
    $cast = app(EnumStateCast::class, ['enum' => StringBackedEnum::class]);

    expect($cast->get(str('one')))
        ->toBe(StringBackedEnum::One);
});

it('can return null if a blank value is passed to the getter', function ($value): void {
    $cast = app(EnumStateCast::class, ['enum' => StringBackedEnum::class]);

    expect($cast->get($value))
        ->toBeNull();
})->with([
    null,
    '',
]);

it('returns `null` for invalid values in `get()`', function ($value): void {
    $cast = app(EnumStateCast::class, ['enum' => StringBackedEnum::class]);

    expect($cast->get($value))->toBeNull();
})->with([
    'unknown backing value' => 'unknown',
    'wrong enum class' => IntegerBackedEnum::One,
    'array' => [['tampered']],
    'object' => new stdClass,
]);

it('returns `null` instead of throwing for a nonnumeric string passed to an integer-backed enum in `get()`', function (): void {
    $cast = app(EnumStateCast::class, ['enum' => IntegerBackedEnum::class]);

    expect($cast->get('not-numeric'))->toBeNull();
});

it('does not coerce booleans or floats to integer-backed enums in `get()`', function ($value): void {
    $cast = app(EnumStateCast::class, ['enum' => IntegerBackedEnum::class]);

    expect($cast->get($value))->toBeNull();
})->with([
    'false' => false,
    'true' => true,
    'zero float' => 0.0,
    'one float' => 1.0,
]);

it('can get the value from a string backed enum in the setter', function (StringBackedEnum $enum, string $string): void {
    $cast = app(EnumStateCast::class, ['enum' => StringBackedEnum::class]);

    expect($cast->set($enum))
        ->toBe($string);
})->with([
    [StringBackedEnum::One, 'one'],
    [StringBackedEnum::Two, 'two'],
    [StringBackedEnum::Three, 'three'],
]);

it('can get the value from an integer backed enum in the setter', function (IntegerBackedEnum $enum, string $value): void {
    $cast = app(EnumStateCast::class, ['enum' => IntegerBackedEnum::class]);

    expect($cast->set($enum))
        ->toBe($value);
})->with([
    [IntegerBackedEnum::Zero, '0'],
    [IntegerBackedEnum::One, '1'],
    [IntegerBackedEnum::Two, '2'],
    [IntegerBackedEnum::Three, '3'],
]);

it('normalizes valid backing and `Stringable` values in `set()`', function ($value, string $expected): void {
    $cast = app(EnumStateCast::class, ['enum' => StringBackedEnum::class]);

    expect($cast->set($value))
        ->toBe($expected);
})->with([
    ['one', 'one'],
    [str('two'), 'two'],
]);

it('normalizes integer-backed zero values in `set()`', function ($value): void {
    $cast = app(EnumStateCast::class, ['enum' => IntegerBackedEnum::class]);

    expect($cast->set($value))->toBe('0');
})->with([
    'integer zero' => 0,
    'numeric string zero' => '0',
]);

it('returns `null` for invalid integer-backed values in `set()`', function ($value): void {
    $cast = app(EnumStateCast::class, ['enum' => IntegerBackedEnum::class]);

    expect($cast->set($value))->toBeNull();
})->with([
    'nonnumeric string' => 'not-numeric',
    'false' => false,
    'true' => true,
    'zero float' => 0.0,
    'one float' => 1.0,
]);

it('returns `null` for invalid values in `set()`', function ($value): void {
    $cast = app(EnumStateCast::class, ['enum' => StringBackedEnum::class]);

    expect($cast->set($value))->toBeNull();
})->with([
    'null' => null,
    'empty string' => '',
    'unknown backing value' => 'unknown',
    'wrong enum class' => IntegerBackedEnum::One,
    'array' => [['tampered']],
    'object' => new stdClass,
]);
