<?php

namespace Filament\Tests\Fixtures\Livewire;

use Closure;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Filament\Tables\Components\TableContent;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Tables\Components\StubTableContent;

/**
 * Renders the given table parts in a custom layout, so a single part can be
 * asserted against without the rest of the default table around it.
 */
class PostsTableWithParts extends PostsTable
{
    /**
     * @var array<Component>
     */
    public static array $parts = [];

    public static ?Closure $configureTable = null;

    public function table(Table $table): Table
    {
        $table = parent::table($table);

        if (static::$configureTable) {
            $table = (static::$configureTable)($table);
        }

        $hasContentPart = array_filter(static::$parts, fn (Component $part): bool => $part instanceof TableContent) !== [];

        return $table->layout(fn (Schema $schema): Schema => $schema->components([
            ...($hasContentPart ? [] : [StubTableContent::make()]),
            ...static::$parts,
        ]));
    }
}
