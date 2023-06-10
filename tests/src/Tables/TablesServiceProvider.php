<?php

namespace Filament\Tests\Tables;

use Filament\Tests\Tables\Fixtures\PostsTable;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Livewire\Mechanisms\ComponentRegistry;

class TablesServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component(app(ComponentRegistry::class)->getName(PostsTable::class), PostsTable::class);
    }
}
