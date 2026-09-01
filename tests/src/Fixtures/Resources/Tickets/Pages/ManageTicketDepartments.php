<?php

namespace Filament\Tests\Fixtures\Resources\Tickets\Pages;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ReplicateAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Resources\Departments\Tables\DepartmentsTable;
use Filament\Tests\Fixtures\Resources\Tickets\TicketResource;

class ManageTicketDepartments extends ManageRelatedRecords
{
    protected static string $resource = TicketResource::class;

    protected static string $relationship = 'departments';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            ViewAction::make(),
            EditAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
            ReplicateAction::make(),
        ];
    }

    public function table(Table $table): Table
    {
        return DepartmentsTable::configure($table);
    }
}
