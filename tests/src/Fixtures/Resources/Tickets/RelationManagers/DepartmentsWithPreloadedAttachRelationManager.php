<?php

namespace Filament\Tests\Fixtures\Resources\Tickets\RelationManagers;

use Filament\Actions\AttachAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Resources\Departments\Tables\DepartmentsTable;

class DepartmentsWithPreloadedAttachRelationManager extends RelationManager
{
    protected static string $relationship = 'departments';

    public function table(Table $table): Table
    {
        return DepartmentsTable::configure($table)
            ->recordTitleAttribute('name')
            ->headerActions([
                AttachAction::make()
                    ->preloadRecordSelect()
                    ->multiple(),
            ]);
    }
}
