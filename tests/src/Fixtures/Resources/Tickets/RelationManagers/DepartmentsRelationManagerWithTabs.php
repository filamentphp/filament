<?php

namespace Filament\Tests\Fixtures\Resources\Tickets\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Resources\Departments\Schemas\DepartmentForm;
use Filament\Tests\Fixtures\Resources\Departments\Tables\DepartmentsTable;

class DepartmentsRelationManagerWithTabs extends RelationManager
{
    protected static string $relationship = 'departments';

    public bool $shouldExcludeTabQueryWhenResolvingRecord = true;

    public function table(Table $table): Table
    {
        return DepartmentsTable::configure($table)
            ->headerActions([
                CreateAction::make(),
            ]);
    }

    public function form(Schema $schema): Schema
    {
        return DepartmentForm::configure($schema);
    }

    public function getTabs(): array
    {
        $aNames = Tab::make('Names starting with A')
            ->modifyQueryUsing(fn ($query) => $query->where('name', 'LIKE', 'A%'));

        $other = Tab::make('Other names')
            ->modifyQueryUsing(fn ($query) => $query->where('name', 'NOT LIKE', 'A%'));

        if ($this->shouldExcludeTabQueryWhenResolvingRecord) {
            $aNames->excludeQueryWhenResolvingRecord();
            $other->excludeQueryWhenResolvingRecord();
        }

        return [
            'a_names' => $aNames,
            'other' => $other,
        ];
    }
}
