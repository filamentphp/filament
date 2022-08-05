<?php

namespace Filament\Forms\Components;

class MultiSelect extends Select
{
    public function isMultiple(): bool
    {
        return true;
    }
}
