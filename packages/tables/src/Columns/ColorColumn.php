<?php

namespace Filament\Tables\Columns;

class ColorColumn extends Column
{
    use Concerns\CanBeCopied;
    use Concerns\CanWrap;

    /**
     * @var view-string
     */
    protected string $view = 'filament-tables::columns.color-column';
}
