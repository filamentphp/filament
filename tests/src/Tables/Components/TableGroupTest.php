<?php

use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Components\TableContentHeader;
use Filament\Tables\Components\TableGroup;
use Filament\Tables\Components\TablePageCheckbox;
use Filament\Tables\Components\TableSearch;
use Filament\Tables\Components\TableSortingSettings;
use Filament\Tables\Components\TableToolbar;
use Filament\Tables\Components\TableToolbarActions;
use Filament\Tables\Table;
use Filament\Tests\Tables\TestCase;

use function Filament\Tests\livewireTableWithParts;

uses(TestCase::class);

it('renders a bare div by default', function (): void {
    $html = livewireTableWithParts([
        TableGroup::make([TableSearch::make()])->id('search-group'),
    ])->html();

    expect($html)
        ->toContain('<div id="search-group">')
        ->not->toContain('fi-growable');
});

it('renders the growable class with `grow()`', function (): void {
    $html = livewireTableWithParts([
        TableToolbar::make([
            TableGroup::make([TableSearch::make()])->id('search-group')->grow(),
        ]),
    ])->html();

    expect($html)->toContain('<div id="search-group" class="fi-growable">');
});

it('merges the growable class with extra classes', function (): void {
    $html = livewireTableWithParts([
        TableGroup::make([TableSearch::make()])
            ->id('search-group')
            ->extraAttributes(['class' => 'custom'])
            ->grow(),
    ])->html();

    expect($html)->toContain('<div id="search-group" class="custom fi-growable">');
});

it('can grow inside a content header', function (): void {
    $html = livewireTableWithParts(
        [
            TableContentHeader::make([
                TableGroup::make([TablePageCheckbox::make()])->id('checkbox-group')->grow(),
                TableSortingSettings::make(),
            ]),
            TableToolbarActions::make(),
        ],
        fn (Table $table): Table => $table
            ->contentGrid(['md' => 2])
            ->columns([
                Stack::make([
                    TextColumn::make('title')->sortable(),
                ]),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]),
    )->html();

    expect($html)
        ->toContain('class="fi-ta-content-header"')
        ->toContain('<div id="checkbox-group" class="fi-growable">')
        ->toContain('fi-ta-sorting-settings');
});

it('renders the inline classes with `inline()`', function (): void {
    $html = livewireTableWithParts([
        TableGroup::make([TableSearch::make()])->id('search-group')->inline()->grow(),
    ])->html();

    expect($html)->toContain('<div id="search-group" class="fi-ta-group fi-inline fi-growable">');
});
