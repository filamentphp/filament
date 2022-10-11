<?php

namespace Filament\Tables\Columns;

/**
 * @deprecated Use IconColumn with the `boolean()` method instead.
 * @see IconColumn
 */
class BooleanColumn extends IconColumn
{
    public function isBoolean(): bool
    {
        return true;
    }
}
