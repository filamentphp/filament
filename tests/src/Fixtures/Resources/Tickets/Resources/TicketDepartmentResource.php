<?php

namespace Filament\Tests\Fixtures\Resources\Tickets\Resources;

use BackedEnum;
use Filament\Resources\ParentResourceRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Department;
use Filament\Tests\Fixtures\Resources\Departments\Schemas\DepartmentForm;
use Filament\Tests\Fixtures\Resources\Departments\Tables\DepartmentsTable;
use Filament\Tests\Fixtures\Resources\Tickets\Resources\TicketDepartmentResource\Pages;
use Filament\Tests\Fixtures\Resources\Tickets\TicketResource;

class TicketDepartmentResource extends Resource
{
    protected static ?string $model = Department::class;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function getParentResourceRegistration(): ?ParentResourceRegistration
    {
        return TicketResource::asParent(static::class)
            ->relationship('departments')
            ->inverseRelationship('tickets');
    }

    public static function form(Schema $schema): Schema
    {
        return DepartmentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DepartmentsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'create' => Pages\CreateTicketDepartment::route('/create'),
            'edit' => Pages\EditTicketDepartment::route('/{record}/edit'),
        ];
    }
}
