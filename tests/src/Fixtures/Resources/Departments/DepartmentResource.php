<?php

namespace Filament\Tests\Fixtures\Resources\Departments;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Department;
use Filament\Tests\Fixtures\Resources\Departments\Pages\CreateDepartment;
use Filament\Tests\Fixtures\Resources\Departments\Pages\EditDepartment;
use Filament\Tests\Fixtures\Resources\Departments\Pages\ListDepartments;
use Filament\Tests\Fixtures\Resources\Departments\Pages\ViewDepartment;
use Filament\Tests\Fixtures\Resources\Departments\Schemas\DepartmentForm;
use Filament\Tests\Fixtures\Resources\Departments\Schemas\DepartmentInfolist;
use Filament\Tests\Fixtures\Resources\Departments\Tables\DepartmentsTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DepartmentResource extends Resource
{
    protected static ?string $model = Department::class;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return DepartmentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DepartmentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DepartmentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDepartments::route('/'),
            'create' => CreateDepartment::route('/create'),
            'view' => ViewDepartment::route('/{record}'),
            'edit' => EditDepartment::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
