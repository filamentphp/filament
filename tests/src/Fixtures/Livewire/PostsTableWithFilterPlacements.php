<?php

namespace Filament\Tests\Fixtures\Livewire;

use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class PostsTableWithFilterPlacements extends PostsTable
{
    public function table(Table $table): Table
    {
        return parent::table($table)
            ->filtersLayout(FiltersLayout::Dropdown)
            ->filters([
                Filter::make('is_published')
                    ->query(fn (EloquentBuilder $query) => $query->where('is_published', true))
                    ->placement(FiltersLayout::AboveContent),
                SelectFilter::make('author')
                    ->relationship('author', 'name'),
            ]);
    }
}
