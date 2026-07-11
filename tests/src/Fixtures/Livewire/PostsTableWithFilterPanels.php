<?php

namespace Filament\Tests\Fixtures\Livewire;

use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\FilterPanel;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class PostsTableWithFilterPanels extends PostsTable
{
    public function table(Table $table): Table
    {
        return parent::table($table)
            ->filters([
                FilterPanel::make(FiltersLayout::AboveContent, [
                    Filter::make('is_published')
                        ->query(fn (EloquentBuilder $query) => $query->where('is_published', true)),
                ])->columns(4),
                FilterPanel::make(FiltersLayout::Dropdown, [
                    SelectFilter::make('author')->relationship('author', 'name'),
                ]),
            ]);
    }
}
