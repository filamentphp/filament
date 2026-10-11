<?php

use Filament\Schemas\Components\StateCasts\EnumArrayStateCast;
use Filament\Tests\Fixtures\Enums\IntegerBackedEnum;
use Filament\Tests\Fixtures\Enums\StringBackedEnum;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('can get an array of enum from strings', function (): void {
    $cast = app(EnumArrayStateCast::class, ['enum' => StringBackedEnum::class]);

    expect($cast->get(['one', 'two', 'three']))
        ->toBe([StringBackedEnum::One, StringBackedEnum::Two, StringBackedEnum::Three]);
});

it('can get an array of enums from integers', function (): void {
    $cast = app(EnumArrayStateCast::class, ['enum' => IntegerBackedEnum::class]);

    expect($cast->get([0, 1, 2, 3]))
        ->toBe([IntegerBackedEnum::Zero, IntegerBackedEnum::One, IntegerBackedEnum::Two, IntegerBackedEnum::Three]);
});

it('can get an array of integer-backed enums from numeric strings', function (): void {
    $cast = app(EnumArrayStateCast::class, ['enum' => IntegerBackedEnum::class]);

    expect($cast->get(['0', '2']))
        ->toBe([IntegerBackedEnum::Zero, IntegerBackedEnum::Two]);
});

it('can ignore if an array of enums is passed to the getter already', function (): void {
    $cast = app(EnumArrayStateCast::class, ['enum' => StringBackedEnum::class]);

    expect($cast->get([StringBackedEnum::One, StringBackedEnum::Two, StringBackedEnum::Three]))
        ->toBe([StringBackedEnum::One, StringBackedEnum::Two, StringBackedEnum::Three]);
});

it('can get an array of enums from `Stringable` values', function (): void {
    $cast = app(EnumArrayStateCast::class, ['enum' => StringBackedEnum::class]);

    expect($cast->get([str('one'), str('two')]))
        ->toBe([StringBackedEnum::One, StringBackedEnum::Two]);
});

it('can return an empty array if blank values are passed to the getter', function (): void {
    $cast = app(EnumArrayStateCast::class, ['enum' => StringBackedEnum::class]);

    expect($cast->get([null, '']))
        ->toBeArray()
        ->toBeEmpty();
});

it('can filter out blank values from the array of enums in the getter', function (): void {
    $cast = app(EnumArrayStateCast::class, ['enum' => StringBackedEnum::class]);

    expect($cast->get(['one', null, 'two', '', 'three']))
        ->toBe([StringBackedEnum::One, StringBackedEnum::Two, StringBackedEnum::Three]);
});

it('filters invalid values from mixed collections in `get()` while preserving valid order', function (): void {
    $cast = app(EnumArrayStateCast::class, ['enum' => StringBackedEnum::class]);

    expect($cast->get([
        'two',
        'unknown',
        IntegerBackedEnum::One,
        ['tampered'],
        new stdClass,
        str('one'),
    ]))->toBe([StringBackedEnum::Two, StringBackedEnum::One]);
});

it('filters invalid integer-backed values without coercing booleans or floats in `get()`', function (): void {
    $cast = app(EnumArrayStateCast::class, ['enum' => IntegerBackedEnum::class]);

    expect($cast->get([0, false, true, 0.0, 1.0, 'not-numeric', '2']))
        ->toBe([IntegerBackedEnum::Zero, IntegerBackedEnum::Two]);
});

it('can decode a JSON array of enum from strings', function (): void {
    $cast = app(EnumArrayStateCast::class, ['enum' => StringBackedEnum::class]);

    expect($cast->get('["one", "two", "three"]'))
        ->toBe([StringBackedEnum::One, StringBackedEnum::Two, StringBackedEnum::Three]);
});

it('can get the values from an array of string backed enums in the setter', function (): void {
    $cast = app(EnumArrayStateCast::class, ['enum' => StringBackedEnum::class]);

    expect($cast->set([StringBackedEnum::One, StringBackedEnum::Two, StringBackedEnum::Three]))
        ->toBe(['one', 'two', 'three']);
});

it('can get the values from an array of integer backed enums in the setter', function (): void {
    $cast = app(EnumArrayStateCast::class, ['enum' => IntegerBackedEnum::class]);

    expect($cast->set([IntegerBackedEnum::Zero, IntegerBackedEnum::One, IntegerBackedEnum::Two, IntegerBackedEnum::Three]))
        ->toBe(['0', '1', '2', '3']);
});

it('normalizes valid backing and `Stringable` values in `set()`', function (): void {
    $cast = app(EnumArrayStateCast::class, ['enum' => StringBackedEnum::class]);

    expect($cast->set(['one', str('two'), 'three']))
        ->toBe(['one', 'two', 'three']);
});

it('can filter out blank values from the array of enums in the setter', function (): void {
    $cast = app(EnumArrayStateCast::class, ['enum' => StringBackedEnum::class]);

    expect($cast->set([StringBackedEnum::One, null, StringBackedEnum::Two, '', StringBackedEnum::Three]))
        ->toBe(['one', 'two', 'three']);
});

it('filters invalid values from mixed collections in `set()` while preserving valid order', function (): void {
    $cast = app(EnumArrayStateCast::class, ['enum' => StringBackedEnum::class]);

    expect($cast->set([
        StringBackedEnum::Two,
        'unknown',
        IntegerBackedEnum::One,
        ['tampered'],
        new stdClass,
        str('one'),
    ]))->toBe(['two', 'one']);
});

it('filters invalid integer-backed values while preserving zero in `set()`', function (): void {
    $cast = app(EnumArrayStateCast::class, ['enum' => IntegerBackedEnum::class]);

    expect($cast->set([0, '0', false, true, 0.0, 1.0, 'not-numeric', '2']))
        ->toBe(['0', '0', '2']);
});

it('normalizes invalid JSON and JSON objects with invalid values to an empty array', function (): void {
    $cast = app(EnumArrayStateCast::class, ['enum' => StringBackedEnum::class]);

    expect($cast->get('{'))->toBe([])
        ->and($cast->set('{'))->toBe([])
        ->and($cast->get('{"key":"unknown"}'))->toBe([])
        ->and($cast->set('{"key":"unknown"}'))->toBe([]);
});
