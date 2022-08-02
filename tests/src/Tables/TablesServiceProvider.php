<?php

namespace Filament\Tests\Tables;

use Filament\Tests\Tables\Fixtures\PostsTable;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class TablesServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component(PostsTable::getName(), PostsTable::class);
    }
}
