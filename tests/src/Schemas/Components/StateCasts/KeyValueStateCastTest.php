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
        'zero' => 0,
        'false' => false,
    ]))
        ->toBe([
            'one' => 'One',
            'zero' => 0,
            'false' => false,
        ]);
});

it('normalizes invalid, scalar, and empty values to an empty array in `get()`', function ($value): void {
    $cast = app(KeyValueStateCast::class);

    expect($cast->get($value))
        ->toBe([]);
})->with([
    'null' => [null],
    'empty string' => '',
    'invalid JSON' => '{',
    'scalar JSON string' => '"value"',
    'scalar JSON integer' => '1',
    'scalar JSON false' => 'false',
    'JSON null' => 'null',
    'empty array' => [[]],
    'empty nested array' => [[[]]],
    'empty JSON array' => '[]',
    'empty JSON object' => '{}',
]);

it('removes empty keys from row lists in `get()` while preserving order and values', function (): void {
    $cast = app(KeyValueStateCast::class);

    expect($cast->get([
        ['key' => 'zero', 'value' => 0],
        ['key' => '', 'value' => 'Ignored'],
        ['key' => 'false', 'value' => false],
    ]))->toBe([
        'zero' => 0,
        'false' => false,
    ]);
});

it('can get an array of objects from a key-value array in the setter', function (): void {
    $cast = app(KeyValueStateCast::class);

    expect($cast->set([
        'one' => 'One',
        'zero' => 0,
        'false' => false,
    ]))
        ->toBe([
            ['key' => 'one', 'value' => 'One'],
            ['key' => 'zero', 'value' => 0],
            ['key' => 'false', 'value' => false],
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

it('normalizes invalid, scalar, and empty values to an empty array in `set()`', function ($value): void {
    $cast = app(KeyValueStateCast::class);

    expect($cast->set($value))
        ->toBe([]);
})->with([
    'null' => [null],
    'empty string' => '',
    'invalid JSON' => '{',
    'scalar JSON string' => '"value"',
    'scalar JSON integer' => '1',
    'scalar JSON false' => 'false',
    'JSON null' => 'null',
    'empty array' => [[]],
    'empty JSON array' => '[]',
    'empty JSON object' => '{}',
]);

it('removes empty keys from maps in `set()` while preserving order and values', function (): void {
    $cast = app(KeyValueStateCast::class);

    expect($cast->set([
        'zero' => 0,
        '' => 'Ignored',
        'false' => false,
    ]))->toBe([
        ['key' => 'zero', 'value' => 0],
        ['key' => 'false', 'value' => false],
    ]);
});
