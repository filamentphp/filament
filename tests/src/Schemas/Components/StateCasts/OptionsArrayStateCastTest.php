<?php

use Filament\Schemas\Components\StateCasts\OptionsArrayStateCast;
use Filament\Tests\Fixtures\Enums\StringBackedEnum;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('casts digit strings to integers and keeps other strings in `get()`', function (): void {
    $cast = app(OptionsArrayStateCast::class);

    expect($cast->get(['1', '2', 'active']))
        ->toBe([1, 2, 'active']);
});

it('resolves `BackedEnum` options to their value in `get()`', function (): void {
    $cast = app(OptionsArrayStateCast::class);

    expect($cast->get([StringBackedEnum::One]))
        ->toBe([StringBackedEnum::One->value]);
});

it('returns an empty array for blank state in `get()`', function (): void {
    $cast = app(OptionsArrayStateCast::class);

    expect($cast->get(null))->toBe([])
        ->and($cast->get([]))->toBe([]);
});

it('drops a tampered non-scalar array element in `get()` instead of throwing', function (): void {
    $cast = app(OptionsArrayStateCast::class);

    // Security: a tampered request can nest an array inside the option values, which would
    // reach `strval()` and throw an `Array to string conversion` error. It must be skipped.
    expect($cast->get([1, ['tampered'], 2]))
        ->toBe([1, 2]);
});

it('casts `Stringable` options to strings in `get()`', function (): void {
    $cast = app(OptionsArrayStateCast::class);

    expect($cast->get([str('active'), str('1')]))
        ->toBe(['active', 1]);
});

it('casts every option to a string in `set()`', function (): void {
    $cast = app(OptionsArrayStateCast::class);

    expect($cast->set([1, 'active']))
        ->toBe(['1', 'active']);
});

it('casts `Stringable` options to strings in `set()`', function (): void {
    $cast = app(OptionsArrayStateCast::class);

    expect($cast->set([str('active'), str('1')]))
        ->toBe(['active', '1']);
});

it('drops a tampered non-scalar array element in `set()` instead of throwing', function (): void {
    $cast = app(OptionsArrayStateCast::class);

    // Security: mirror `get()` and fail closed on a nested array so `strval()` cannot crash.
    expect($cast->set([1, ['tampered'], 2]))
        ->toBe(['1', '2']);
});
