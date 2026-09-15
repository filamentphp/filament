<?php

use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Components\TableContent;
use Filament\Tables\Components\TablePageCheckbox;
use Filament\Tables\Components\TableToolbar;
use Filament\Tables\Components\TableToolbarActions;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Tables\TestCase;

use function Filament\Tests\livewireTableWithParts;

uses(TestCase::class);

function configureSelectableGridTable(Table $table): Table
{
    return $table
        ->contentGrid(['md' => 2])
        ->columns([
            Stack::make([
                TextColumn::make('title'),
            ]),
        ])
        ->bulkActions([
            DeleteBulkAction::make(),
        ]);
}

it('renders the select all checkbox for a selectable table with a content grid', function (): void {
    $html = livewireTableWithParts(
        [TablePageCheckbox::make(), TableToolbarActions::make()],
        configureSelectableGridTable(...),
    )->html();

    expect($html)
        ->toContain('class="fi-ta-page-checkbox fi-checkbox-input"')
        ->toContain('x-on:click="toggleSelectRecordsOnPage"');
});

it('renders nothing for a row table', function (): void {
    $html = livewireTableWithParts(
        [TablePageCheckbox::make(), TableToolbarActions::make()],
        fn (Table $table): Table => $table->bulkActions([DeleteBulkAction::make()]),
    )->html();

    expect($html)->not->toContain('fi-ta-page-checkbox');
});

it('renders nothing when records cannot be selected', function (): void {
    $html = livewireTableWithParts(
        [TablePageCheckbox::make()],
        fn (Table $table): Table => $table
            ->contentGrid(['md' => 2])
            ->columns([
                Stack::make([
                    TextColumn::make('title'),
                ]),
            ]),
    )->html();

    expect($html)->not->toContain('fi-ta-page-checkbox');
});

it('is not repeated in the content header when placed in a toolbar', function (): void {
    Post::factory()->count(2)->create();

    $html = livewireTableWithParts(
        [
            TableToolbar::make([TablePageCheckbox::make(), TableToolbarActions::make()]),
            TableContent::make(),
        ],
        configureSelectableGridTable(...),
    )->html();

    expect($html)
        ->toContain('class="fi-ta-page-checkbox fi-checkbox-input"')
        ->not->toContain('fi-ta-content-header');
});
