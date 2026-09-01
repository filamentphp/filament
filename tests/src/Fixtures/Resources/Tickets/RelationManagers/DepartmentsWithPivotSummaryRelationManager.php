<?php

namespace Filament\Tests\Fixtures\Resources\Tickets\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DepartmentsWithPivotSummaryRelationManager extends RelationManager
{
    protected static string $relationship = 'departments';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                // Test implicit pivot column (just `quantity`)
                TextColumn::make('quantity')
                    ->summarize(Sum::make('quantity_sum')),
                // Test explicit pivot column (`pivot.price`)
                TextColumn::make('pivot.price')
                    ->summarize(Sum::make('price_sum')),
            ]);
    }
}
