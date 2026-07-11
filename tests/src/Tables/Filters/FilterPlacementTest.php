<?php

use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tests\Fixtures\Livewire\PostsTable;
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
