<?php

namespace Filament\Tests\Fixtures\Livewire;

use Filament\Schemas\Schema;
use Filament\Tables\Table;

class PostsTableWithIncompleteLayout extends PostsTable
{
    public function table(Table $table): Table
    {
        return parent::table($table)
            ->layout(fn (Schema $schema): Schema => $schema->components([]));
    }
}
