<?php

namespace Filament\Tests\Fixtures\Resources\Tickets\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Resources\Departments\Schemas\DepartmentForm;
use Filament\Tests\Fixtures\Resources\Departments\Tables\DepartmentsTable;
use Illuminate\Database\Eloquent\Model;

class DepartmentsWithDeferredBadgeRelationManager extends RelationManager
{
    protected static string $relationship = 'departments';

    protected static bool $isBadgeDeferred = true;

    public static function getBadge(Model $ownerRecord, string $pageClass): ?string
    {
        $count = $ownerRecord->departments()->count();

        return $count > 0 ? (string) $count : null;
    }

    public function table(Table $table): Table
    {
        return DepartmentsTable::configure($table);
    }

    public function form(Schema $schema): Schema
    {
        return DepartmentForm::configure($schema);
    }
}
