<?php

namespace Filament\Forms\Components;

/**
 * @deprecated use Select with the `multiple()` method instead.
 */
class MultiSelect extends Select
{
    public function isMultiple(): bool
    {
        return true;
    }
}
