<?php

namespace Filament\Tests\Fixtures\Pages;

use Filament\Pages\Page;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\ReorderableColumnsTable;

class ColumnManagerReorderingBrowserTest extends Page
{
    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Livewire::make(ReorderableColumnsTable::class, [
                'deferred' => request()->boolean('deferred', true),
                'persist' => request()->boolean('persist', true),
                'toggleable' => request()->boolean('toggleable', true),
                'layout' => request()->query('layout', 'dropdown'),
            ])->key('reorderableColumns')->id('reorderable-columns'),
        ]);
    }
}
