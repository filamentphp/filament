<?php

namespace Filament\Tests\Fixtures\Resources\Tickets\RelationManagers;

use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ContentTabsDepartmentsRelationManager extends DepartmentsRelationManager
{
    public string $activeLocale = 'en';

    public string $heading = 'Default departments';

    public static function getBadge(Model $ownerRecord, string $pageClass): ?string
    {
        return 'Managers';
    }

    public function table(Table $table): Table
    {
        return parent::table($table)->heading("{$this->heading} ({$this->activeLocale})");
    }
}
