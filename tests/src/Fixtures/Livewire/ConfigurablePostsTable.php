<?php

namespace Filament\Tests\Fixtures\Livewire;

use Closure;
use Filament\Tables\Table;

/**
 * Applies a per-test configuration after the base table is built, so a single
 * option can be varied without a fixture class per option.
 */
class ConfigurablePostsTable extends PostsTable
{
    public static ?Closure $configureTable = null;

    public function table(Table $table): Table
    {
        $table = parent::table($table);

        if (static::$configureTable) {
            $table = (static::$configureTable)($table);
        }

        return $table;
    }
}
