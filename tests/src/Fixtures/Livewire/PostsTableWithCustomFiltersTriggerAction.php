<?php

namespace Filament\Tests\Fixtures\Livewire;

use Filament\Actions\Action;
use Filament\Tables\Table;

class PostsTableWithCustomFiltersTriggerAction extends PostsTable
{
    public function table(Table $table): Table
    {
        return parent::table($table)
            ->filtersTriggerAction(
                fn (Action $action) => $action
                    ->label('Show filters')
                    ->button(),
            );
    }
}
