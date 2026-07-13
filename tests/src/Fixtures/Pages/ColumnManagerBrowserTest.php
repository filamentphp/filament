<?php

namespace Filament\Tests\Fixtures\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tests\Fixtures\Livewire\PostsColumnManagerTable;
use Filament\Tests\Fixtures\Livewire\SecondPostsColumnManagerTable;

class ColumnManagerBrowserTest extends Page
{
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedViewColumns;

    protected static ?int $navigationSort = 10;

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Two tables sharing column names prove that column manager checkbox ids stay scoped to their own table.
                Livewire::make(PostsColumnManagerTable::class)
                    ->key('firstTable')
                    ->id('first-table'),
                Livewire::make(SecondPostsColumnManagerTable::class)
                    ->key('secondTable')
                    ->id('second-table'),
            ]);
    }
}
