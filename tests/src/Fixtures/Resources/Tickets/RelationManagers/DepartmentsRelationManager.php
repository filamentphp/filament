<?php

namespace Filament\Tests\Fixtures\Resources\Tickets\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Resources\Departments\Tables\DepartmentsTable;

class DepartmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'departments';

    public function table(Table $table): Table
    {
        return DepartmentsTable::configure($table)
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
