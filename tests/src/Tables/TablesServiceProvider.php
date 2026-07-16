<?php

namespace Filament\Tests\Tables;

use Filament\Tests\Fixtures\Livewire\PostsColumnManagerTable;
use Filament\Tests\Fixtures\Livewire\PostsTable;
use Filament\Tests\Fixtures\Livewire\SecondPostsColumnManagerTable;
use Illuminate\Support\ServiceProvider;
use Livewire\Finder\Finder;
use Livewire\Livewire;

class TablesServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        foreach ([
            PostsTable::class,
            PostsColumnManagerTable::class,
            SecondPostsColumnManagerTable::class,
        ] as $component) {
            [$namespace, $componentName] = app(Finder::class)->parseNamespaceAndName($component);

            Livewire::component($componentName, $component);
        }
    }
}
