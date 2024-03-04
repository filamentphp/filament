<?php

use Filament\Tests\Infolists\Fixtures\IdEntry;
use Filament\Tests\Tables\TestCase;

uses(TestCase::class);

it('can be instantiated with a default name', function () {

    $entry = IdEntry::make();

    expect($entry->getName())
        ->toBe('ID');
});

test('default name can be overridden', function () {

    $entry = IdEntry::make('Identifier');

    expect($entry->getName())
        ->toBe('Identifier');
});
