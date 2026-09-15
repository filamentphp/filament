<?php

use Filament\Tables\Components\TableReorderTrigger;
use Filament\Tables\Table;
use Filament\Tests\Tables\TestCase;

use function Filament\Tests\livewireTableWithParts;

uses(TestCase::class);

it('renders the reorder trigger for a reorderable table', function (): void {
    $html = livewireTableWithParts(
        [TableReorderTrigger::make()],
        fn (Table $table): Table => $table->reorderable('sort'),
    )->html();

    expect($html)->toContain('wire:click="toggleTableReordering"');
});

it('renders nothing for a table that is not reorderable', function (): void {
    $html = livewireTableWithParts([TableReorderTrigger::make()])->html();

    expect($html)->not->toContain('toggleTableReordering');
});
