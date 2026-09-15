<?php

use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Components\TableContentHeader;
use Filament\Tables\Components\TablePageCheckbox;
use Filament\Tables\Components\TablePagination;
use Filament\Tables\Components\TablePaginationLinks;
use Filament\Tables\Components\TablePaginationOverview;
use Filament\Tables\Components\TableSearch;
use Filament\Tables\Components\TableSortingSettings;
use Filament\Tables\Components\TableSplit;
use Filament\Tables\Components\TableToolbar;
use Filament\Tables\Components\TableToolbarActions;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Tables\TestCase;

use function Filament\Tests\livewireTableWithParts;

uses(TestCase::class);

/**
 * Parts render with whitespace and Livewire block markers around their markup, which the assertions ignore.
 */
function compactSplitHtml(string $html): string
{
    return preg_replace(['/<!--.*?-->/s', '/>\s+</'], ['', '><'], $html);
}

it('renders its parts in growing cells by default', function (): void {
    $html = livewireTableWithParts([
        TableSplit::make([
            TableSearch::make(),
            TableToolbarActions::make()->grow(false),
        ])->id('split'),
    ])->html();

    $html = compactSplitHtml($html);

    expect($html)
        ->toContain('<div id="split" class="fi-ta-split fi-ta-layout-split default:fi-ta-split"><div class="fi-growable"><div')
        ->toContain('fi-ta-search-field')
        ->toContain('</div><div><');

    expect(substr_count($html, '<div class="fi-growable">'))->toBe(1);
});

it('stacks below the given breakpoint', function (): void {
    $html = livewireTableWithParts([
        TableSplit::make([TableSearch::make()])->id('split')->from('md'),
    ])->html();

    expect($html)->toContain('<div id="split" class="fi-ta-split fi-ta-layout-split md:fi-ta-split">');
});

it('leaves no cell behind for a part that renders nothing', function (): void {
    $html = livewireTableWithParts(
        [TableSplit::make([TableSearch::make(), TableSortingSettings::make()])->id('split')],
        fn (Table $table): Table => $table->columns([TextColumn::make('title')->searchable()]),
    )->html();

    expect(substr_count(compactSplitHtml($html), '<div class="fi-growable">'))->toBe(1);
});

it('pushes the sort selects to the end of a content header', function (): void {
    $html = livewireTableWithParts(
        [
            TableContentHeader::make([
                TableSplit::make([
                    TablePageCheckbox::make(),
                    TableSortingSettings::make()->grow(false),
                ]),
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

    expect(compactSplitHtml($html))
        ->toContain('class="fi-ta-content-header"')
        ->toContain('<div class="fi-growable"><input')
        ->toContain('<div><div')
        ->toContain('class="fi-ta-sorting-settings"');
});

it('lays out a composed pagination', function (): void {
    Post::factory()->count(15)->create();

    $html = livewireTableWithParts([
        TablePagination::make([
            TableSplit::make([
                TablePaginationOverview::make(),
                TablePaginationLinks::make()->grow(false),
            ]),
        ]),
    ])->html();

    expect(compactSplitHtml($html))
        ->toContain('class="fi-pagination fi-ta-pagination"')
        ->toContain('fi-ta-layout-split')
        ->toContain('<div class="fi-growable"><div class="fi-pagination-part">')
        ->toContain('fi-pagination-items');
});

it('is a group of its own inside a toolbar', function (): void {
    $html = livewireTableWithParts([
        TableToolbar::make([
            TableSplit::make([TableSearch::make()]),
        ]),
    ])->html();

    expect(compactSplitHtml($html))
        ->toContain('fi-ta-layout-split')
        ->not->toContain('<div class="fi-ta-actions fi-align-start fi-wrapped">');
});
