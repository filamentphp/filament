<?php

namespace Filament\Tests\Fixtures\Livewire;

use Filament\Tables\Table;

class PostsTableWithDeferredLoading extends PostsTable
{
    public function table(Table $table): Table
    {
        return parent::table($table)
            ->deferLoading();
    }
}
