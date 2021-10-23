<?php

namespace Filament\Tables\Columns;

class Badge extends Text
{
    use Concerns\CanFormatState;
    use Concerns\HasColors;

    protected string $view = 'tables::columns.badge';
}
