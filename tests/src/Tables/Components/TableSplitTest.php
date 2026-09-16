<?php

use Filament\Tables\Columns\TextColumn;
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

it('keeps a cell for a part that renders nothing, so the row does not shift', function (): void {
    $html = livewireTableWithParts(
        [TableSplit::make([TableSortingSettings::make(), TableSearch::make()])->id('split')],
        fn (Table $table): Table => $table->columns([TextColumn::make('title')->searchable()]),
    )->html();

    $html = compactSplitHtml($html);

    expect($html)->toContain('<div id="split" class="fi-ta-split fi-ta-layout-split default:fi-ta-split"><div class="fi-growable"></div><div class="fi-growable"><div');
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

it('keeps its row when every part renders nothing, and the row around it can still hide', function (): void {
    Post::factory()->count(15)->create();

    $html = livewireTableWithParts(
        [
            TablePagination::make([
                TableSplit::make([TableSortingSettings::make()])->id('split'),
            ]),
        ],
        fn (Table $table): Table => $table->columns([TextColumn::make('title')]),
    )->html();

    expect($html)->not->toContain('fi-ta-pagination');
});
