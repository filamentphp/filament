<?php

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Components\TableColumnManager;
use Filament\Tables\Table;
use Filament\Tests\Tables\TestCase;

use function Filament\Tests\livewireTableWithParts;

uses(TestCase::class);

it('renders the column manager trigger for a table with toggleable columns', function (): void {
    $html = livewireTableWithParts([TableColumnManager::make()])->html();

    expect($html)
        ->toContain('fi-ta-col-manager-dropdown')
        ->toContain('x-data="filamentTableColumnManager(');
});

it('renders nothing for a table without toggleable columns', function (): void {
    $html = livewireTableWithParts(
        [TableColumnManager::make()],
        fn (Table $table): Table => $table->columns([TextColumn::make('title')]),
    )->html();

    expect($html)->not->toContain('fi-ta-col-manager');
});
