<?php

use Filament\Schemas\Schema;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tests\Fixtures\Livewire\PostsTable;
use Filament\Tests\Fixtures\Livewire\PostsTableWithCustomFiltersFormSchemaAndPlacements;
use Filament\Tests\Fixtures\Livewire\PostsTableWithFilterPlacements;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Tables\TestCase;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('returns the explicitly set `placement()`', function (): void {
    $filter = Filter::make('is_published')->placement(FiltersLayout::AboveContent);

    expect($filter->getPlacement())->toBe(FiltersLayout::AboveContent);
});

it('evaluates a `Closure` passed to `placement()`', function (): void {
    $filter = Filter::make('is_published')->placement(fn (): FiltersLayout => FiltersLayout::BelowContent);

    expect($filter->getPlacement())->toBe(FiltersLayout::BelowContent);
});

it('falls back to the table `filtersLayout()` when no `placement()` is set', function (): void {
    $livewire = livewire(PostsTable::class);

    $table = $livewire->instance()->getTable();

    // `PostsTable` sets no `filtersLayout()`, so the default is `Dropdown`.
    expect($table->getFilter('is_published')->getPlacement())->toBe(FiltersLayout::Dropdown);
});

it('keeps a flat filters form when every filter shares one placement', function (): void {
    // `PostsTable` sets no per-filter placement, so all filters resolve to `Dropdown`.
    $livewire = livewire(PostsTable::class);

    $keys = collect($livewire->instance()->getTableFiltersForm()->getComponents(withHidden: true))
        ->map(fn ($component): ?string => $component->getKey(isAbsolute: false))
        ->all();

    // No container wrapping: top-level components are the filter groups themselves, keyed by filter name.
    expect($keys)->not->toContain('placement::Dropdown')->toContain('is_published');
});

it('wraps filters in one container per placement when placements differ', function (): void {
    $livewire = livewire(PostsTableWithFilterPlacements::class);

    $keys = collect($livewire->instance()->getTableFiltersForm()->getComponents(withHidden: true))
        ->map(fn ($component): ?string => $component->getKey(isAbsolute: false))
        ->all();

    expect($keys)->toContain('placement::AboveContent')->toContain('placement::Dropdown');
});

it('still filters the query when filters are split across placements', function (): void {
    $posts = Post::factory()->count(10)->create();

    livewire(PostsTableWithFilterPlacements::class)
        ->assertCanSeeTableRecords($posts)
        ->filterTable('is_published')
        ->assertCanSeeTableRecords($posts->where('is_published', true))
        ->assertCanNotSeeTableRecords($posts->where('is_published', false));
});

it('lists the active filter placements', function (): void {
    $table = livewire(PostsTableWithFilterPlacements::class)->instance()->getTable();

    expect($table->getActiveFilterPlacements())
        ->toContain(FiltersLayout::AboveContent)
        ->toContain(FiltersLayout::Dropdown);
});

it('returns a scoped child schema per placement', function (): void {
    $table = livewire(PostsTableWithFilterPlacements::class)->instance()->getTable();

    $aboveContent = $table->getFiltersFormForPlacement(FiltersLayout::AboveContent);

    expect($aboveContent)->toBeInstanceOf(Schema::class);
    expect($aboveContent->toHtml())
        ->toContain('is_published')
        ->not->toContain('tableFilters.author')
        ->not->toContain('tableDeferredFilters.author');
});

it('returns the whole form for the only placement when placements do not differ', function (): void {
    $table = livewire(PostsTable::class)->instance()->getTable();

    // `PostsTable` resolves every filter to `Dropdown`; the flat form is returned as-is.
    expect($table->getFiltersFormForPlacement(FiltersLayout::Dropdown))
        ->toBe($table->getFiltersForm());
});

it('counts active filters per placement, so a dialog badge ignores filters placed elsewhere', function (): void {
    Post::factory()->count(5)->create();

    $table = livewire(PostsTableWithFilterPlacements::class)
        ->filterTable('is_published')
        ->instance()
        ->getTable();

    expect($table->getActiveFiltersCount())->toBe(1);

    expect($table->getActiveFiltersCountForPlacement(FiltersLayout::Dropdown))->toBe(0);
    expect($table->getActiveFiltersCountForPlacement(FiltersLayout::AboveContent))->toBe(1);
});

it('resets only the filters belonging to the given placement', function (): void {
    $posts = Post::factory()->count(10)->create();

    $author = $posts->first()->author;

    $livewire = livewire(PostsTableWithFilterPlacements::class)
        ->filterTable('is_published')      // placed `AboveContent`
        ->filterTable('author', $author);  // placed `Dropdown`

    // Both filters are active to begin with.
    expect($livewire->instance()->getTable()->getActiveFiltersCount())->toBe(2);

    // Reset only the `AboveContent` panel.
    $livewire->call('resetTableFiltersForm', 'AboveContent');

    $table = $livewire->instance()->getTable();

    // The `AboveContent` filter is cleared; the `Dropdown` filter is untouched.
    expect($table->getActiveFiltersCountForPlacement(FiltersLayout::AboveContent))->toBe(0);
    expect($table->getActiveFiltersCountForPlacement(FiltersLayout::Dropdown))->toBe(1);
});

it('ignores per-filter placement when a custom `filtersFormSchema()` is set', function (): void {
    $livewire = livewire(PostsTableWithCustomFiltersFormSchemaAndPlacements::class);

    $keys = collect($livewire->instance()->getTableFiltersForm()->getComponents(withHidden: true))
        ->map(fn ($component): ?string => $component->getKey(isAbsolute: false))
        ->all();

    expect(collect($keys)->contains(fn (?string $key): bool => str_starts_with((string) $key, 'placement::')))
        ->toBeFalse();
});
