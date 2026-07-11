<?php

use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Enums\FiltersResetActionPosition;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\FilterPanel;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Livewire\PostsTable;
use Filament\Tests\Fixtures\Livewire\PostsTableWithFilterPanels;
use Filament\Tests\Fixtures\Models\Post;
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

it('merges panels that share a location, keeping the first panel\'s configuration', function (): void {
    $table = Table::make(livewire(PostsTable::class)->instance())
        ->filters([
            FilterPanel::make(FiltersLayout::AboveContent, [Filter::make('a')])->columns(4),
            FilterPanel::make(FiltersLayout::AboveContent, [Filter::make('b')]),
        ]);

    $panels = $table->getFilterPanels();

    expect($panels)->toHaveCount(1);
    expect($panels[0]->getColumns())->toBe(4); // The first panel's config wins.
    expect(array_map(fn ($filter): string => $filter->getName(), $panels[0]->getFilters()))->toBe(['a', 'b']);
    expect(array_keys($table->getFilters()))->toContain('a', 'b');
});

it('merges a pushed panel into the existing panel at the same location', function (): void {
    $table = Table::make(livewire(PostsTable::class)->instance())
        ->filters([
            FilterPanel::make(FiltersLayout::AboveContent, [Filter::make('a')])->columns(3),
            FilterPanel::make(FiltersLayout::Dropdown, [Filter::make('b')]),
        ]);

    // A plugin appends another panel at an already-occupied location.
    $table->pushFilters([
        FilterPanel::make(FiltersLayout::AboveContent, [Filter::make('c')])->columns(1),
    ]);

    $abovePanel = collect($table->getFilterPanels())
        ->first(fn ($panel): bool => $panel->getLocation() === FiltersLayout::AboveContent);

    expect($abovePanel->getColumns())->toBe(3); // The existing panel's config wins.
    expect(array_map(fn ($filter): string => $filter->getName(), $abovePanel->getFilters()))->toBe(['a', 'c']);
    expect(array_keys($table->getFilters()))->toContain('a', 'b', 'c');
});

it('builds one container per panel keyed by location', function (): void {
    $keys = collect(livewire(PostsTableWithFilterPanels::class)->instance()->getTableFiltersForm()->getComponents(withHidden: true))
        ->map(fn ($component): ?string => $component->getKey(isAbsolute: false))
        ->all();

    expect($keys)->toContain('filterPanel::AboveContent')->toContain('filterPanel::Dropdown');
});

it('builds a flat schema for a flat, single-panel table', function (): void {
    $keys = collect(livewire(PostsTable::class)->instance()->getTableFiltersForm()->getComponents(withHidden: true))
        ->map(fn ($component): ?string => $component->getKey(isAbsolute: false))
        ->all();

    expect($keys)->not->toContain('filterPanel::Dropdown')->toContain('is_published');
});

it('resolves per-panel `columns()` with fallthrough', function (): void {
    $table = livewire(PostsTableWithFilterPanels::class)->instance()->getTable();

    [$abovePanel, $dropdownPanel] = $table->getFilterPanels();

    // `AboveContent` panel sets `columns(4)` explicitly.
    expect($table->getFiltersFormColumnsForPanel($abovePanel))->toBe(4);
    // `Dropdown` panel sets nothing => falls through to the per-layout default (1 for a dialog).
    expect($table->getFiltersFormColumnsForPanel($dropdownPanel))->toBe(1);
});

it('counts active filters per panel', function (): void {
    Post::factory()->count(5)->create();

    $table = livewire(PostsTableWithFilterPanels::class)
        ->filterTable('is_published')
        ->instance()
        ->getTable();

    [$abovePanel, $dropdownPanel] = $table->getFilterPanels();

    expect($table->getActiveFiltersCountForPanel($abovePanel))->toBe(1);
    expect($table->getActiveFiltersCountForPanel($dropdownPanel))->toBe(0);
});

it('returns a scoped child schema per panel', function (): void {
    $table = livewire(PostsTableWithFilterPanels::class)->instance()->getTable();

    $aboveContent = $table->getFiltersFormForPanel($table->getFilterPanels()[0]);

    expect($aboveContent)->toBeInstanceOf(Schema::class);
    expect($aboveContent->toHtml())
        ->toContain('is_published')
        ->not->toContain('tableFilters.author');
});

it('throws when `filtersFormSchema()` is combined with panels', function (): void {
    $table = Table::make(livewire(PostsTable::class)->instance())
        ->filters([
            FilterPanel::make(FiltersLayout::AboveContent, [Filter::make('a')]),
            FilterPanel::make(FiltersLayout::Dropdown, [Filter::make('b')]),
        ])
        ->filtersFormSchema(fn (array $filters): array => array_values($filters));

    expect(fn () => $table->getFiltersFormSchema())->toThrow(LogicException::class);
});

it('resets only the filters belonging to the given panel', function (): void {
    $posts = Post::factory()->count(10)->create();

    $author = $posts->first()->author;

    $livewire = livewire(PostsTableWithFilterPanels::class)
        ->filterTable('is_published')      // AboveContent panel
        ->filterTable('author', $author);  // Dropdown panel

    expect($livewire->instance()->getTable()->getActiveFiltersCount())->toBe(2);

    $livewire->call('resetTableFiltersForm', 'AboveContent');

    $table = $livewire->instance()->getTable();

    [$abovePanel, $dropdownPanel] = $table->getFilterPanels();

    expect($table->getActiveFiltersCountForPanel($abovePanel))->toBe(0);
    expect($table->getActiveFiltersCountForPanel($dropdownPanel))->toBe(1);
});

it('scopes the filter trigger reset action to its panel location', function (): void {
    $table = livewire(PostsTableWithFilterPanels::class)->instance()->getTable();

    $resetAction = collect($table->getFiltersTriggerAction(FiltersLayout::Dropdown)->getExtraModalFooterActions())
        ->first(fn ($action): bool => $action->getName() === 'resetFilters');

    expect($resetAction?->getLivewireClickHandler())->toBe("resetTableFiltersForm('Dropdown')");
});

it('keeps the filter trigger reset action global when no location is given', function (): void {
    $table = livewire(PostsTableWithFilterPanels::class)->instance()->getTable();

    $resetAction = collect($table->getFiltersTriggerAction()->getExtraModalFooterActions())
        ->first(fn ($action): bool => $action->getName() === 'resetFilters');

    expect($resetAction?->getLivewireClickHandler())->toBe('resetTableFiltersForm');
});

it('normalises a flat `filters($filters, layout:)` array to an implicit panel at that layout', function (): void {
    $table = Table::make(livewire(PostsTable::class)->instance())
        ->filters([Filter::make('a'), Filter::make('b')], layout: FiltersLayout::AboveContent);

    $panels = $table->getFilterPanels();

    expect($panels)->toHaveCount(1);
    expect($panels[0]->getLocation())->toBe(FiltersLayout::AboveContent);
    expect(count($panels[0]->getFilters()))->toBe(2);
});

it('removes filters from every panel with the global "remove all"', function (): void {
    $posts = Post::factory()->count(10)->create();

    $author = $posts->first()->author;

    $livewire = livewire(PostsTableWithFilterPanels::class)
        ->filterTable('is_published')      // AboveContent panel
        ->filterTable('author', $author);  // Dropdown panel

    expect($livewire->instance()->getTable()->getActiveFiltersCount())->toBe(2);

    $livewire->call('removeTableFilters');

    expect($livewire->instance()->getTable()->getActiveFiltersCount())->toBe(0);
});

it('throws when `pushFilters()` adds a loose filter to a panel-mode table', function (): void {
    $table = Table::make(livewire(PostsTable::class)->instance())
        ->filters([FilterPanel::make(FiltersLayout::AboveContent, [Filter::make('a')])]);

    expect(fn () => $table->pushFilters([Filter::make('b')]))->toThrow(LogicException::class);
});

it('throws when `pushFilters()` adds a panel to a flat-mode table', function (): void {
    $table = Table::make(livewire(PostsTable::class)->instance())
        ->filters([Filter::make('a')]);

    expect(fn () => $table->pushFilters([
        FilterPanel::make(FiltersLayout::Dropdown, [Filter::make('b')]),
    ]))->toThrow(LogicException::class);
});

it('resolves per-panel `width()`, `maxHeight()` and `resetActionPosition()` with fallthrough', function (): void {
    $table = Table::make(livewire(PostsTable::class)->instance())
        ->filtersFormMaxHeight('300px')
        ->filters([
            FilterPanel::make(FiltersLayout::AboveContent, [Filter::make('a')])
                ->width(Width::Large)
                ->maxHeight('500px')
                ->resetActionPosition(FiltersResetActionPosition::Footer),
            FilterPanel::make(FiltersLayout::Dropdown, [Filter::make('b')]),
        ]);

    [$abovePanel, $dropdownPanel] = $table->getFilterPanels();

    // Panel overrides win.
    expect($table->getFiltersFormWidthForPanel($abovePanel))->toBe(Width::Large);
    expect($table->getFiltersFormMaxHeightForPanel($abovePanel))->toBe('500px');
    expect($table->getResetActionPositionForPanel($abovePanel))->toBe(FiltersResetActionPosition::Footer);

    // Unset falls through to the table-level default, then the per-layout default.
    expect($table->getFiltersFormMaxHeightForPanel($dropdownPanel))->toBe('300px');
    expect($table->getResetActionPositionForPanel($dropdownPanel))->toBe(FiltersResetActionPosition::Header);
});
