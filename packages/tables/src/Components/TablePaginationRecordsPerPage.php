<?php

namespace Filament\Tables\Components;

class TablePaginationRecordsPerPage extends TablePart
{
    protected function getPartView(): string
    {
        return 'filament-tables::components.parts.pagination-records-per-page';
    }
}
