<?php

namespace Filament\Tests\Tables;

use Filament\Tests\Fixtures\Livewire\PostsTable;
use Illuminate\Support\ServiceProvider;
use Livewire\Finder\Finder;
use Livewire\Livewire;

class TablesServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        [$namespace, $componentName] = app(Finder::class)->parseNamespaceAndName(PostsTable::class);

        Livewire::component($componentName, PostsTable::class);
    }
}
