<?php

namespace Filament\Tests\Fixtures\Livewire;

use Filament\Actions\Action;
use Filament\Tables\Table;

class PostsTableWithUnauthorizedFiltersResetAction extends PostsTable
{
    public function table(Table $table): Table
    {
        return parent::table($table)
            ->filtersResetAction(
                static fn (Action $action) => $action
                    ->authorize(false)
                    ->authorizationMessage('You cannot reset filters')
                    ->authorizationNotification(),
            );
    }
}
