<?php

namespace Filament\Tests\Fixtures\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tests\Fixtures\Livewire\PostsTableWithCustomLayout;

class CustomTableLayoutBrowserTest extends Page
{
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedTableCells;

    protected static ?int $navigationSort = 11;

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Livewire::make(PostsTableWithCustomLayout::class)
                    ->key('table')
                    ->id('table'),
            ]);
    }
}
