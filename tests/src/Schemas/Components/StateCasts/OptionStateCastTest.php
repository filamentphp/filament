<?php

use Filament\Schemas\Components\StateCasts\OptionStateCast;
use Filament\Tests\Fixtures\Enums\StringBackedEnum;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('casts digit strings to integers and keeps other strings in `get()`', function (): void {
    $cast = app(OptionStateCast::class);

    expect($cast->get('1'))->toBe(1)
        ->and($cast->get('active'))->toBe('active');
});

it('resolves a `BackedEnum` option to its value in `get()`', function (): void {
    $cast = app(OptionStateCast::class);

    expect($cast->get(StringBackedEnum::One))
        ->toBe(StringBackedEnum::One->value);
});

it('casts a `Stringable` option to a string in `get()`', function (): void {
    $cast = app(OptionStateCast::class);

    expect($cast->get(str('active')))->toBe('active')
        ->and($cast->get(str('1')))->toBe(1);
});

it('returns `null` for blank state in `get()`', function (): void {
    $cast = app(OptionStateCast::class);

    expect($cast->get(null))->toBeNull()
        ->and($cast->get(''))->toBeNull();
});

it('returns `null` for a tampered non-scalar value in `get()` instead of throwing', function (): void {
    $cast = app(OptionStateCast::class);

    // Security: a tampered request can deliver an array, which would reach `strval()` and
    // throw an `Array to string conversion` error. It must be treated as no selection.
    expect($cast->get(['tampered']))->toBeNull();
});

it('casts the option to a string in `set()`', function (): void {
    $cast = app(OptionStateCast::class);

    expect($cast->set(1))->toBe('1')
        ->and($cast->set('active'))->toBe('active');
});

it('resolves a `BackedEnum` option to its value in `set()`', function (): void {
    $cast = app(OptionStateCast::class);

    expect($cast->set(StringBackedEnum::One))
        ->toBe(StringBackedEnum::One->value);
});

it('casts a `Stringable` option to a string in `set()`', function (): void {
    $cast = app(OptionStateCast::class);

    expect($cast->set(str('active')))->toBe('active');
});

it('returns `null` for a tampered non-scalar value in `set()` instead of throwing', function (): void {
    $cast = app(OptionStateCast::class);

    // Security: mirror `get()` and fail closed on a tampered array so `strval()` cannot crash.
    expect($cast->set(['tampered']))->toBeNull();
});
