<?php

namespace Filament\Tests\Fixtures\Livewire;

use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;

class PostsTableWithBeforeContentFilters extends PostsTable
{
    public function table(Table $table): Table
    {
        return parent::table($table)
            ->filtersLayout(FiltersLayout::BeforeContent);
    }
}
