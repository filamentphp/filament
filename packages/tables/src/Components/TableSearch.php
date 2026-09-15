<?php

namespace Filament\Tables\Components;

class TableSearch extends TablePart
{
    protected function getPartView(): string
    {
        return 'filament-tables::components.parts.search';
    }
}
