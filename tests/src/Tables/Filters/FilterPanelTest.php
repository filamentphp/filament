<?php

use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\FilterPanel;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Livewire\PostsTable;
use Filament\Tests\Fixtures\Livewire\PostsTableWithFilterPanels;
use Filament\Tests\Tables\TestCase;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('holds a location and its filters', function (): void {
    $panel = FilterPanel::make(FiltersLayout::AboveContent, [
        Filter::make('is_published'),
        Filter::make('is_featured'),
    ]);

    expect($panel->getLocation())->toBe(FiltersLayout::AboveContent);
    expect(array_map(fn ($filter): string => $filter->getName(), $panel->getFilters()))
        ->toBe(['is_published', 'is_featured']);
});

it('returns `null` for unset panel config by default', function (): void {
    $panel = FilterPanel::make(FiltersLayout::Dropdown, [Filter::make('a')]);

    expect($panel->getColumns())->toBeNull();
    expect($panel->getWidth())->toBeNull();
    expect($panel->getMaxHeight())->toBeNull();
    expect($panel->getResetActionPosition())->toBeNull();
});

it('stores per-panel `columns()`', function (): void {
    $panel = FilterPanel::make(FiltersLayout::AboveContent, [Filter::make('a')])->columns(4);

    expect($panel->getColumns())->toBe(4);
});

it('flattens panel filters into the name-keyed map so `getFilters()` is unchanged in shape', function (): void {
    $table = livewire(PostsTableWithFilterPanels::class)->instance()->getTable();

    expect(array_keys($table->getFilters()))->toContain('is_published', 'author');
    expect($table->getFilter('author'))->toBeInstanceOf(SelectFilter::class);
});

it('exposes the panels via `getFilterPanels()`', function (): void {
    $table = livewire(PostsTableWithFilterPanels::class)->instance()->getTable();

    $panels = $table->getFilterPanels();

    expect($panels)->toHaveCount(2);
    expect($panels[0]->getLocation())->toBe(FiltersLayout::AboveContent);
    expect($panels[1]->getLocation())->toBe(FiltersLayout::Dropdown);
});

it('normalises a flat `filters()` array into one implicit panel at the table layout', function (): void {
    // `PostsTable` uses a flat filters array and no `filtersLayout()`, so the default is `Dropdown`.
    $table = livewire(PostsTable::class)->instance()->getTable();

    $panels = $table->getFilterPanels();

    expect($panels)->toHaveCount(1);
    expect($panels[0]->getLocation())->toBe(FiltersLayout::Dropdown);
    expect(count($panels[0]->getFilters()))->toBe(count($table->getFilters()));
});

it('throws when mixing a `FilterPanel` and a loose filter in `filters()`', function (): void {
    $table = Table::make(livewire(PostsTable::class)->instance());

    expect(fn () => $table->filters([
        FilterPanel::make(FiltersLayout::AboveContent, [Filter::make('a')]),
        Filter::make('b'),
    ]))->toThrow(LogicException::class);
});

it('throws when two panels target the same location', function (): void {
    $table = Table::make(livewire(PostsTable::class)->instance());

    expect(fn () => $table->filters([
        FilterPanel::make(FiltersLayout::AboveContent, [Filter::make('a')]),
        FilterPanel::make(FiltersLayout::AboveContent, [Filter::make('b')]),
    ]))->toThrow(LogicException::class);
});
