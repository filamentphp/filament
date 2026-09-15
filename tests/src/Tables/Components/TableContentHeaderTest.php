<?php

use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Components\TableContent;
use Filament\Tables\Components\TableContentHeader;
use Filament\Tables\Components\TablePageCheckbox;
use Filament\Tables\Components\TableToolbar;
use Filament\Tables\Components\TableToolbarActions;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Tables\TestCase;

use function Filament\Tests\livewireTableWithParts;

uses(TestCase::class);

function configureSelectableSortableGridTable(Table $table): Table
{
    return $table
        ->contentGrid(['md' => 2])
        ->columns([
            Stack::make([
                TextColumn::make('title')->sortable(),
            ]),
        ])
        ->bulkActions([
            DeleteBulkAction::make(),
        ]);
}

it('renders the select all checkbox and the sort selects by default', function (): void {
    $html = livewireTableWithParts(
        [TableContentHeader::make(), TableToolbarActions::make()],
        configureSelectableSortableGridTable(...),
    )->html();

    expect($html)
        ->toContain('class="fi-ta-content-header"')
        ->toContain('class="fi-ta-page-checkbox fi-checkbox-input"')
        ->toContain('class="fi-ta-sorting-settings"');
});

it('renders only the given items', function (): void {
    $html = livewireTableWithParts(
        [TableContentHeader::make([TablePageCheckbox::make()]), TableToolbarActions::make()],
        configureSelectableSortableGridTable(...),
    )->html();

    expect($html)
        ->toContain('class="fi-ta-content-header"')
        ->toContain('class="fi-ta-page-checkbox fi-checkbox-input"')
        ->not->toContain('fi-ta-sorting-settings');
});

it('renders nothing when its items render nothing', function (): void {
    $html = livewireTableWithParts([TableContentHeader::make(), TableToolbarActions::make()])->html();

    expect($html)->not->toContain('fi-ta-content-header');
});

it('replaces the header row above the records when placed', function (): void {
    Post::factory()->count(2)->create();

    $html = livewireTableWithParts(
        [
            TableToolbar::make([TableToolbarActions::make()]),
            TableContentHeader::make(),
            TableContent::make(),
        ],
        configureSelectableSortableGridTable(...),
    )->html();

    expect(substr_count($html, 'class="fi-ta-content-header"'))->toBe(1);
    expect(strpos($html, 'fi-ta-content-header'))->toBeLessThan(strpos($html, 'fi-ta-content-ctn'));
});
