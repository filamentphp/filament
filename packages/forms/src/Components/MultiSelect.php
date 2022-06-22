<?php

namespace Filament\Forms\Components;

/**
 * @deprecated Use `\Filament\Forms\Components\Select` and `->multiple()` instead.
 * @see Select
 */
class MultiSelect extends Select
{
    public function isMultiple(): bool
    {
        return true;
    }
}
