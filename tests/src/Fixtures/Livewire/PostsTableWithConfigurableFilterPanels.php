<?php

namespace Filament\Tests\Fixtures\Livewire;

use Closure;
use Filament\Tables\Table;

class PostsTableWithConfigurableFilterPanels extends PostsTable
{
    public static ?Closure $configureUsing = null;

    public function table(Table $table): Table
    {
        $table = parent::table($table);

        if (static::$configureUsing) {
            $table = (static::$configureUsing)($table);
        }

        return $table;
    }
}
