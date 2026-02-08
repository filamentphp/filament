<?php

namespace Filament\Tests\Fixtures\Resources\Tickets\RelationManagers;

use Filament\Actions\DetachAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Resources\Departments\Tables\DepartmentsTable;

class DepartmentsWithDetachActionRelationManager extends RelationManager
{
    protected static string $relationship = 'departments';

    public function table(Table $table): Table
    {
        return DepartmentsTable::configure($table)
            ->recordActions([
                DetachAction::make(),
            ]);
    }
}
