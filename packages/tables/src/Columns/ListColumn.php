<?php

namespace Filament\Tables\Columns;

class ListColumn extends Column
{
    use Concerns\CanFormatState;

    /**
     * @var view-string
     */
    protected string $view = 'filament-tables::columns.list-column';
}
