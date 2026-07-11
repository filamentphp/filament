<?php

use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tests\Fixtures\Livewire\PostsTable;
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
