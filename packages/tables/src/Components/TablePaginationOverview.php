<?php

namespace Filament\Tables\Components;

class TablePaginationOverview extends TablePart
{
    protected function getPartView(): string
    {
        return 'filament-tables::components.parts.pagination-overview';
    }
}
