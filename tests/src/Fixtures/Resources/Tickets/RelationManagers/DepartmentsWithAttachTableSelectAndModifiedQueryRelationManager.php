<?php

namespace Filament\Tests\Fixtures\Resources\Tickets\RelationManagers;

use Filament\Actions\AttachAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Resources\Departments\Tables\DepartmentsTable;
use Illuminate\Database\Eloquent\Builder;

class DepartmentsWithAttachTableSelectAndModifiedQueryRelationManager extends RelationManager
{
    protected static string $relationship = 'departments';

    public int $attachActionProcessCount = 0;

    public int $attachActionAfterCount = 0;

    public function table(Table $table): Table
    {
        return DepartmentsTable::configure($table)
            ->headerActions([
                AttachAction::make()
                    ->tableSelect(DepartmentsTable::class)
                    ->recordSelectOptionsQuery(fn (Builder $query) => $query->where('name', 'like', 'Active%'))
                    ->using(function (): void {
                        $this->attachActionProcessCount++;
                    })
                    ->after(function (): void {
                        $this->attachActionAfterCount++;
                    }),
            ]);
    }
}
