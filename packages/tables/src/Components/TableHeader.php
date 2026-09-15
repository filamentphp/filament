<?php

namespace Filament\Tables\Components;

class TableHeader extends TablePart
{
    protected function getPartView(): string
    {
        return 'filament-tables::components.parts.header';
    }
}
