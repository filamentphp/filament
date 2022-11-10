<?php

namespace Filament\Tables\Columns;

class ColorColumn extends Column
{
    use Concerns\CanBeCopied;
    use Concerns\HasSummary;

    protected string $view = 'tables::columns.color-column';
}
