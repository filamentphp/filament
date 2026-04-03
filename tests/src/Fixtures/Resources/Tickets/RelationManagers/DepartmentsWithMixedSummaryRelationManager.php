<?php

namespace Filament\Tests\Fixtures\Resources\Tickets\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\Summarizers\Count;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DepartmentsWithMixedSummaryRelationManager extends RelationManager
{
    protected static string $relationship = 'departments';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->summarize(Count::make('name_count')),
                TextColumn::make('quantity')
                    ->summarize(Sum::make('quantity_sum')),
                TextColumn::make('pivot.price')
                    ->summarize(Sum::make('price_sum')),
            ]);
    }
}
