<?php

namespace Filament\Tables\Components;

class TableContent extends TablePart
{
    protected function getPartView(): string
    {
        return 'filament-tables::components.parts.content';
    }
}
