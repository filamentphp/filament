<?php

namespace Filament\Tables\Columns;

class TextColumn extends Column
{
    use Concerns\CanFormatState;

    protected string $view = 'tables::columns.text-column';
}
