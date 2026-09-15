<?php

namespace Filament\Tables\Components;

class TableColumnManager extends TablePart
{
    protected function getPartView(): string
    {
        return 'filament-tables::components.parts.column-manager';
    }
}
