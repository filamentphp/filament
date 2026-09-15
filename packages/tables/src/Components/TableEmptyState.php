<?php

namespace Filament\Tables\Components;

class TableEmptyState extends TablePart
{
    protected function getPartView(): string
    {
        return 'filament-tables::components.parts.empty-state';
    }
}
