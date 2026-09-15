<?php

namespace Filament\Tables\Components;

class TablePageCheckbox extends TablePart
{
    protected function getPartView(): string
    {
        return 'filament-tables::components.parts.page-checkbox';
    }
}
