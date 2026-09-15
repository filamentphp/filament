<?php

namespace Filament\Tables\Components;

class TablePaginationLinks extends TablePart
{
    protected function getPartView(): string
    {
        return 'filament-tables::components.parts.pagination-links';
    }
}
