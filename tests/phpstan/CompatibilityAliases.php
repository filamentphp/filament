<?php

namespace Filament\Tests\PHPStan;

use Filament\Forms\Components\Concerns\CanLimitItemsCount;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\Concerns\HasRowLoop;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\CanBeLazyLoaded as LivewireCanBeLazyLoaded;
use Filament\Tables\Table;
use Filament\Tables\Table\Concerns\CanBeLazyLoaded;
use Filament\Tests\Fixtures\Livewire\PostsTable;

class ItemCountSelect extends Select
{
    use CanLimitItemsCount;
}

class RowLoopTextColumn extends TextColumn
{
    use HasRowLoop;
}

class LazyLoadedTable extends Table
{
    use CanBeLazyLoaded;
}

class LazyLoadedTableComponent extends PostsTable
{
    use LivewireCanBeLazyLoaded;
}
