<?php

namespace Filament\Tables\Columns;

class BadgeColumn extends TextColumn
{
    use Concerns\CanFormatState;
    use Concerns\HasColor;
    use Concerns\HasIcon;

    protected string $view = 'tables::columns.badge-column';
}
