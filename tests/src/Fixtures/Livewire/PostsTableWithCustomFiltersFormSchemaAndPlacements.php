<?php

namespace Filament\Tests\Fixtures\Livewire;

use Filament\Tables\Table;

class PostsTableWithCustomFiltersFormSchemaAndPlacements extends PostsTableWithFilterPlacements
{
    public function table(Table $table): Table
    {
        return parent::table($table)
            ->filtersFormSchema(fn (array $filters): array => array_values($filters));
    }
}
