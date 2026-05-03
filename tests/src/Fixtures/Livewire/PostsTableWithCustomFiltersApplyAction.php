<?php

namespace Filament\Tests\Fixtures\Livewire;

use Filament\Actions\Action;
use Filament\Tables\Table;

class PostsTableWithCustomFiltersApplyAction extends PostsTable
{
    public function table(Table $table): Table
    {
        return parent::table($table)
            ->filtersApplyAction(
                fn (Action $action) => $action
                    ->label('Apply filters'),
            );
    }
}
