<?php

namespace Filament\Tables\Components;

class TableReorderTrigger extends TablePart
{
    protected function getPartView(): string
    {
        return 'filament-tables::components.parts.reorder-trigger';
    }
}
