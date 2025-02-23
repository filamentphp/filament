<?php

use Filament\Schemas\Components\StateCasts\KeyValueStateCast;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('can get a key-value array from an array of objects', function (): void {
    $cast = app(KeyValueStateCast::class);

    expect($cast->get([
        ['key' => 'one', 'value' => 'One'],
        ['key' => 'two', 'value' => 'Two'],
        ['key' => 'three', 'value' => 'Three'],
    ]))
        ->toBe([
            'one' => 'One',
            'two' => 'Two',
            'three' => 'Three',
        ]);
});

it('can decode a JSON string to a key-value array', function (): void {
    $cast = app(KeyValueStateCast::class);

    expect($cast->get('[
        {"key": "one", "value": "One"},
        {"key": "two", "value": "Two"},
        {"key": "three", "value": "Three"}
    ]'))
        ->toBe([
            'one' => 'One',
            'two' => 'Two',
            'three' => 'Three',
        ]);
});

it('does not decode an array if it is already in key-value format', function (): void {
    $cast = app(KeyValueStateCast::class);

    expect($cast->get([
        'one' => 'One',
        'two' => 'Two',
        'three' => 'Three',
    ]))
        ->toBe([
            'one' => 'One',
            'two' => 'Two',
            'three' => 'Three',
        ]);
});

it('returns an empty array if the value is blank', function ($value): void {
    $cast = app(KeyValueStateCast::class);

    expect($cast->get($value))
        ->toBe([]);
})->with([
    null,
    '',
    [[]],
]);

it('can get an array of objects from a key-value array in the setter', function (): void {
    $cast = app(KeyValueStateCast::class);

    expect($cast->set([
        'one' => 'One',
        'two' => 'Two',
        'three' => 'Three',
    ]))
        ->toBe([
            ['key' => 'one', 'value' => 'One'],
            ['key' => 'two', 'value' => 'Two'],
            ['key' => 'three', 'value' => 'Three'],
        ]);
});

it('can decode a JSON string to an array of objects in the setter', function (): void {
    $cast = app(KeyValueStateCast::class);

    expect($cast->set('{
        "one": "One",
        "two": "Two",
        "three": "Three"
    }'))
        ->toBe([
            ['key' => 'one', 'value' => 'One'],
            ['key' => 'two', 'value' => 'Two'],
            ['key' => 'three', 'value' => 'Three'],
        ]);
});

it('does not decode an array if it is already in object format in the setter', function (): void {
    $cast = app(KeyValueStateCast::class);

    expect($cast->set([
        ['key' => 'one', 'value' => 'One'],
        ['key' => 'two', 'value' => 'Two'],
        ['key' => 'three', 'value' => 'Three'],
    ]))
        ->toBe([
            ['key' => 'one', 'value' => 'One'],
            ['key' => 'two', 'value' => 'Two'],
            ['key' => 'three', 'value' => 'Three'],
        ]);
});
