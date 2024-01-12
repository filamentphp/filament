<?php

namespace Filament\Widgets;

use Filament\Actions;
use Filament\Forms;
use Filament\Infolists;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;

class TableWidget extends Widget implements Actions\Contracts\HasActions, Forms\Contracts\HasForms, Infolists\Contracts\HasInfolists, Tables\Contracts\HasTable
{
    use Actions\Concerns\InteractsWithActions;
    use Forms\Concerns\InteractsWithForms;
    use Infolists\Concerns\InteractsWithInfolists;
    use Tables\Concerns\InteractsWithTable {
        makeTable as makeBaseTable;
    }

    /**
     * @var view-string
     */
    protected static string $view = 'filament-widgets::table-widget';

    /**
     * @deprecated Override the `table()` method to configure the table.
     */
    protected static ?string $heading = null;

    protected function paginateTableQuery(Builder $query): Paginator | CursorPaginator
    {
        return $query->simplePaginate(($this->getTableRecordsPerPage() === 'all') ? $query->count() : $this->getTableRecordsPerPage());
    }

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
                    ->title(),
            );
    }
}
