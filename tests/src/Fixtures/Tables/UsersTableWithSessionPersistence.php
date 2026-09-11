<?php

namespace Filament\Tests\Fixtures\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\User;
use Illuminate\Database\Eloquent\Builder;

class UsersTableWithSessionPersistence
{
    public static function configure(Table $table): Table
    {
        return $table
            ->query(User::query())
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Filter::make('has_name')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('name')),
            ])
            ->groups([
                Group::make('name'),
            ])
            ->persistFiltersInSession()
            ->persistSearchInSession()
            ->persistColumnSearchesInSession()
            ->persistSortInSession()
            ->persistGroupInSession()
            ->persistColumnsInSession();
    }
}
