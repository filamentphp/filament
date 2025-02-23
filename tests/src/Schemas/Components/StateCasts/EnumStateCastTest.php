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
    [1, IntegerBackedEnum::One],
    [2, IntegerBackedEnum::Two],
    [3, IntegerBackedEnum::Three],
]);

it('can ignore if an enum is passed to the getter already', function (StringBackedEnum $enum): void {
    $cast = app(EnumStateCast::class, ['enum' => StringBackedEnum::class]);

    expect($cast->get($enum))
        ->toBe($enum);
})->with([
    StringBackedEnum::One,
    StringBackedEnum::Two,
    StringBackedEnum::Three,
]);

it('can return null if a blank value is passed to the getter', function ($value): void {
    $cast = app(EnumStateCast::class, ['enum' => StringBackedEnum::class]);

    expect($cast->get($value))
        ->toBeNull();
})->with([
    null,
    '',
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

it('can get the value from an integer backed enum in the setter', function (IntegerBackedEnum $enum, int $integer): void {
    $cast = app(EnumStateCast::class, ['enum' => IntegerBackedEnum::class]);

    expect($cast->set($enum))
        ->toBe($integer);
})->with([
    [IntegerBackedEnum::One, 1],
    [IntegerBackedEnum::Two, 2],
    [IntegerBackedEnum::Three, 3],
]);

it('can ignore the value in the setter if it is not an enum', function ($value): void {
    $cast = app(EnumStateCast::class, ['enum' => StringBackedEnum::class]);

    expect($cast->set($value))
        ->toBe($value);
})->with([
    null,
    '',
    'one',
]);
