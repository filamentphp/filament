<?php

namespace Filament\Tables\Columns;

class ColorColumn extends Column
{
    use Concerns\CanBeCopied;

    /**
     * @var view-string $view
     */
    protected string $view = 'filament-tables::columns.color-column';
}
