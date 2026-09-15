<?php

use Filament\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
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
