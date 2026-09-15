<?php

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Components\TableSearch;
use Filament\Tables\Table;
use Filament\Tests\Tables\TestCase;

use function Filament\Tests\livewireTableWithParts;

uses(TestCase::class);

it('renders the search input bound to `tableSearch`', function (): void {
    $html = livewireTableWithParts([TableSearch::make()])->html();

    expect($html)
        ->toContain('class="fi-ta-search-field"')
        ->toContain('wire:model.live.debounce.500ms="tableSearch"');
});

it('renders nothing when no column is searchable', function (): void {
    $html = livewireTableWithParts(
        [TableSearch::make()],
        fn (Table $table): Table => $table->columns([TextColumn::make('title')]),
    )->html();

    expect($html)->not->toContain('fi-ta-search-field');
});

it('binds the input on blur with `searchOnBlur()`', function (): void {
    $html = livewireTableWithParts(
        [TableSearch::make()],
        fn (Table $table): Table => $table->searchOnBlur(),
    )->html();

    expect($html)->toContain('wire:model.live.blur="tableSearch"');
});
