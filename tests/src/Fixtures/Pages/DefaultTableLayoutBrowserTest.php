<?php

namespace Filament\Tests\Fixtures\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tests\Fixtures\Livewire\PostsTable;
use Filament\Tests\Fixtures\Livewire\PostsTableWithBeforeContentFilters;

class DefaultTableLayoutBrowserTest extends Page
{
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedTableCells;

    protected static ?int $navigationSort = 12;

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Livewire::make(PostsTable::class)
                    ->key('defaultTable')
                    ->id('default-table'),
                Livewire::make(PostsTableWithBeforeContentFilters::class)
                    ->key('sidebarFiltersTable')
                    ->id('sidebar-filters-table'),
            ]);
    }
}
