<?php

use Filament\Infolists\Components\TextEntry;
use Filament\Tests\Tables\TestCase;

uses(TestCase::class);

it('can be instantiated with a default name', function (): void {
    $entry = IdEntry::make();

    expect($entry->getName())
        ->toBe('id');
});

test('can ignore the default name if another is specified', function (): void {
    $entry = IdEntry::make('identifier');

    expect($entry->getName())
        ->toBe('identifier');
});

class IdEntry extends TextEntry
{
    public static function getDefaultName(): ?string
    {
        return 'id';
    }
}
