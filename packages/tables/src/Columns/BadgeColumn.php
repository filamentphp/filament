<?php

namespace Filament\Tables\Columns;

class BadgeColumn extends TextColumn
{
    use Concerns\CanFormatState;
    use Concerns\HasColors;

    protected string $view = 'tables::columns.badge-column';
}
