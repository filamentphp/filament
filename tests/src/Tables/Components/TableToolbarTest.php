<?php

use Filament\Actions\BulkAction;
use Filament\Schemas\Components\Flex;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Components\TableColumnManager;
use Filament\Tables\Components\TableGroup;
use Filament\Tables\Components\TableSearch;
use Filament\Tables\Components\TableToolbar;
use Filament\Tables\Table;
use Filament\Tests\Tables\TestCase;

use function Filament\Tests\livewireTableWithParts;

uses(TestCase::class);

it('renders the default toolbar items', function (): void {
    $html = livewireTableWithParts([TableToolbar::make()])->html();

    expect($html)
        ->toContain('class="fi-ta-header-toolbar"')
        ->toContain('class="fi-ta-search-field"')
        ->toContain('fi-ta-filters-dropdown')
        ->toContain('class="fi-ta-grouping-settings"')
        ->toContain('fi-ta-col-manager-dropdown');
});

it('renders only the given items in a custom toolbar', function (): void {
    $html = livewireTableWithParts([TableToolbar::make([TableSearch::make()])])->html();

    expect($html)
        ->toContain('class="fi-ta-header-toolbar"')
        ->toContain('class="fi-ta-search-field"')
        ->not->toContain('fi-ta-filters-dropdown')
        ->not->toContain('fi-ta-grouping-settings')
        ->not->toContain('fi-ta-col-manager-dropdown');
});

it('shows the toolbar only while records are selected when it holds bulk actions only', function (): void {
    $html = livewireTableWithParts(
        [TableToolbar::make()],
        fn (Table $table): Table => $table
            ->columns([TextColumn::make('title')])
            ->filters([])
            ->groups([])
            ->toolbarActions([BulkAction::make('first'), BulkAction::make('second')]),
    )->html();

    expect($html)
        ->toContain('x-show="false || false || (getSelectedRecordsCount() && 2)"')
        ->toContain('.table.header-toolbar.selection"');
});

it('renders a nested layout component as a toolbar group of its own', function (): void {
    $html = livewireTableWithParts([
        TableToolbar::make([
            TableSearch::make(),
            Flex::make([TableColumnManager::make()]),
        ]),
    ])->html();

    $actionsGroupPosition = strpos($html, '<div class="fi-ta-actions fi-align-start fi-wrapped">');
    $searchPosition = strpos($html, 'fi-ta-search-field');
    $flexPosition = strpos($html, 'fi-sc-flex');
    $columnManagerPosition = strpos($html, 'fi-ta-col-manager-dropdown');

    expect(substr_count($html, '<div class="fi-ta-actions fi-align-start fi-wrapped">'))->toBe(1);
    expect($actionsGroupPosition)->toBeLessThan($searchPosition);
    expect($searchPosition)->toBeLessThan($flexPosition);
    expect($flexPosition)->toBeLessThan($columnManagerPosition);
});

it('renders no actions group when the toolbar holds layout components only', function (): void {
    $html = livewireTableWithParts([
        TableToolbar::make([
            TableGroup::make([TableSearch::make()]),
        ]),
    ])->html();

    expect($html)
        ->toContain('class="fi-ta-search-field"')
        ->not->toContain('<div class="fi-ta-actions fi-align-start fi-wrapped">');
});
