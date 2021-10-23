<?php

namespace Filament\Tables\Columns;

class Text extends Column
{
    use Concerns\CanFormatState;

    protected string $view = 'tables::columns.text';
}
