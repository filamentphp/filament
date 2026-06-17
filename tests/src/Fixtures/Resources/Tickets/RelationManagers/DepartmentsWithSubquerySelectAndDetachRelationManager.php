<?php

namespace Filament\Tests\Fixtures\Resources\Tickets\RelationManagers;

use Filament\Actions\DetachAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Resources\Departments\Tables\DepartmentsTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class DepartmentsWithSubquerySelectAndDetachRelationManager extends RelationManager
{
    protected static string $relationship = 'departments';

    public function table(Table $table): Table
    {
        return DepartmentsTable::configure($table)
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->addSelect([
                'ticket_count' => DB::table('department_ticket')
                    ->whereColumn('department_ticket.department_id', 'departments.id')
                    ->selectRaw('count(*)'),
            ]))
            ->recordActions([
                DetachAction::make(),
            ]);
    }
}
