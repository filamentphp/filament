<?php

namespace Filament\Tables\Components;

class TableSelectionIndicator extends TablePart
{
    protected function getPartView(): string
    {
        return 'filament-tables::components.parts.selection-indicator';
    }
}
