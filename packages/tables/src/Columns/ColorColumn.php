<?php

namespace Filament\Tables\Columns;

class ColorColumn extends Column
{
    use Concerns\CanBeCopied;

    protected string $view = 'filament-tables::columns.color-column';
}
