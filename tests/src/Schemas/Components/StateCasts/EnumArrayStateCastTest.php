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

    expect($cast->get([1, 2, 3]))
        ->toBe([IntegerBackedEnum::One, IntegerBackedEnum::Two, IntegerBackedEnum::Three]);
});

it('can ignore if an array of enums is passed to the getter already', function (): void {
    $cast = app(EnumArrayStateCast::class, ['enum' => StringBackedEnum::class]);

    expect($cast->get([StringBackedEnum::One, StringBackedEnum::Two, StringBackedEnum::Three]))
        ->toBe([StringBackedEnum::One, StringBackedEnum::Two, StringBackedEnum::Three]);
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

    expect($cast->set([IntegerBackedEnum::One, IntegerBackedEnum::Two, IntegerBackedEnum::Three]))
        ->toBe([1, 2, 3]);
});

it('can ignore the values in the setter if they are not enums', function (): void {
    $cast = app(EnumArrayStateCast::class, ['enum' => StringBackedEnum::class]);

    expect($cast->set(['one', 'two', 'three']))
        ->toBe(['one', 'two', 'three']);
});

it('can filter out blank values from the array of enums in the setter', function (): void {
    $cast = app(EnumArrayStateCast::class, ['enum' => StringBackedEnum::class]);

    expect($cast->set([StringBackedEnum::One, null, StringBackedEnum::Two, '', StringBackedEnum::Three]))
        ->toBe(['one', 'two', 'three']);
});
