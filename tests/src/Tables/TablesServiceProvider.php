<?php

namespace Filament\Tests\Tables;

use Filament\Tests\Fixtures\Livewire\PostsColumnManagerTable;
use Filament\Tests\Fixtures\Livewire\PostsTable;
use Filament\Tests\Fixtures\Livewire\SecondPostsColumnManagerTable;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Livewire\Mechanisms\ComponentRegistry;

class TablesServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Livewire::component(app(ComponentRegistry::class)->getName(PostsTable::class), PostsTable::class);
        Livewire::component(app(ComponentRegistry::class)->getName(PostsColumnManagerTable::class), PostsColumnManagerTable::class);
        Livewire::component(app(ComponentRegistry::class)->getName(SecondPostsColumnManagerTable::class), SecondPostsColumnManagerTable::class);
    }
}
