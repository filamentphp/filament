<?php

namespace Filament\Tests\Fixtures\Resources\Tickets\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Resources\Departments\Schemas\DepartmentForm;
use Filament\Tests\Fixtures\Resources\Departments\Tables\DepartmentsTable;

class DepartmentsRelationManagerWithPreservation extends RelationManager
{
    protected static string $relationship = 'departments';

    public function table(Table $table): Table
    {
        return DepartmentsTable::configure($table)
            ->headerActions([
                CreateAction::make()
                    ->preserveFormDataWhenCreatingAnother(['name']),
            ]);
    }

    public function form(Schema $schema): Schema
    {
        return DepartmentForm::configure($schema);
    }
}
