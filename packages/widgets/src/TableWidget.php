<?php

namespace Filament\Widgets;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Enums\PaginationMode;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;

class TableWidget extends Widget implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use InteractsWithTable {
        makeTable as makeBaseTable;
    }

    /**
     * @var view-string
     */
    protected string $view = 'filament-widgets::table-widget';

    /**
     * @deprecated Override the `table()` method to configure the table.
     */
    protected static ?string $heading = null;

    /**
     * @deprecated Override the `table()` method to configure the table.
     */
    protected function getTableHeading(): string | Htmlable | null
    {
        return static::$heading;
    }

    protected function makeTable(): Table
    {
        return $this->makeBaseTable()
            ->heading(
                $this->getTableHeading() ?? (string) str(class_basename(static::class))
                    ->beforeLast('Widget')
                    ->kebab()
                    ->replace('-', ' ')
                    ->ucwords(),
            )
            ->paginationMode(PaginationMode::Simple);
    }
}
