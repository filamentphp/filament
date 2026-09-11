<?php

namespace Filament\Tests\Fixtures\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Post;
use Illuminate\Database\Eloquent\Builder;

class PostsTableWithSessionPersistence
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(Post::query())
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Filter::make('is_published')
                    ->query(fn (Builder $query): Builder => $query->where('is_published', true)),
            ])
            ->groups([
                Group::make('title'),
            ])
            ->persistFiltersInSession()
            ->persistSearchInSession()
            ->persistColumnSearchesInSession()
            ->persistSortInSession()
            ->persistGroupInSession()
            ->persistColumnsInSession();
    }
}
