<?php

use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Components\TableContent;
use Filament\Tables\Components\TableContentHeader;
use Filament\Tables\Components\TablePageCheckbox;
use Filament\Tables\Components\TableSortingSettings;
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

/**
 * Parts render with whitespace and Livewire block markers around their markup, which the assertions ignore.
 */
function compactContentHeaderHtml(string $html): string
{
    return preg_replace(['/<!--.*?-->/s', '/>\s+</', '/\s+/'], ['', '><', ' '], $html);
}

it('renders the select all checkbox and the sort selects at the start of the row by default', function (): void {
    $html = livewireTableWithParts(
        [TableContentHeader::make(), TableToolbarActions::make()],
        configureSelectableSortableGridTable(...),
    )->html();

    expect(compactContentHeaderHtml($html))
        ->toContain('<div class="fi-ta-split fi-ta-layout-split default:fi-ta-split fi-ta-content-header"><div><input')
        ->toContain('class="fi-ta-page-checkbox fi-checkbox-input"')
        ->toContain('<div><div x-data')
        ->toContain('class="fi-ta-sorting-settings"')
        ->not->toContain('fi-growable');
});

it('lays the given items out like a split', function (): void {
    $html = livewireTableWithParts(
        [
            TableContentHeader::make([
                TablePageCheckbox::make(),
                TableSortingSettings::make()->grow(false),
            ])->from('md'),
            TableToolbarActions::make(),
        ],
        configureSelectableSortableGridTable(...),
    )->html();

    expect(compactContentHeaderHtml($html))
        ->toContain('<div class="fi-ta-split fi-ta-layout-split md:fi-ta-split fi-ta-content-header"><div class="fi-growable"><input')
        ->toContain('<div><div x-data')
        ->toContain('class="fi-ta-sorting-settings"');
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

    expect(substr_count($html, 'fi-ta-content-header'))->toBe(1);
    expect(strpos($html, 'fi-ta-content-header'))->toBeLessThan(strpos($html, 'fi-ta-content-ctn'));
});
