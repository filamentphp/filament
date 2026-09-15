<?php

namespace Filament\Tables\Components;

class TablePagination extends TablePart
{
    protected function getPartView(): string
    {
        return 'filament-tables::components.parts.pagination';
    }
}
