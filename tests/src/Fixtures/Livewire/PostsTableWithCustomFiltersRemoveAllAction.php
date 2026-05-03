<?php

namespace Filament\Tests\Fixtures\Livewire;

use Filament\Actions\Action;
use Filament\Tables\Table;

class PostsTableWithCustomFiltersRemoveAllAction extends PostsTable
{
    public function table(Table $table): Table
    {
        return parent::table($table)
            ->filtersRemoveAllAction(
                fn (Action $action) => $action
                    ->label('Clear filters')
                    ->link(),
            );
    }
}
