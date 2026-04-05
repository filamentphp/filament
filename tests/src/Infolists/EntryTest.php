<?php

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
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

describe('hint icon tooltip', function (): void {
    it('can set a hint icon tooltip via `hintIcon()` second parameter', function (): void {
        $entry = TextEntry::make('test')
            ->container(Schema::make(Livewire::make()))
            ->hintIcon('heroicon-o-information-circle', 'Example tooltip');

        expect($entry->getHintIconTooltip())
            ->toBe('Example tooltip');
    });

    it('does not clear a previously set hint icon tooltip when calling `hintIcon()` without a tooltip', function (): void {
        $entry = TextEntry::make('test')
            ->container(Schema::make(Livewire::make()))
            ->hintIconTooltip('Example tooltip')
            ->hintIcon('heroicon-o-information-circle');

        expect($entry->getHintIconTooltip())
            ->toBe('Example tooltip');
    });

    it('can clear a previously set hint icon tooltip by explicitly passing `null` to `hintIcon()`', function (): void {
        $entry = TextEntry::make('test')
            ->container(Schema::make(Livewire::make()))
            ->hintIconTooltip('Example tooltip')
            ->hintIcon('heroicon-o-information-circle', null);

        expect($entry->getHintIconTooltip())
            ->toBeNull();
    });
});

class IdEntry extends TextEntry
{
    public static function getDefaultName(): ?string
    {
        return 'id';
    }
}
