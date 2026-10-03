<?php

namespace Filament\Tables\Columns;

use Illuminate\Database\Eloquent\Model;

/**
 * @deprecated Use `IconColumn` with the `boolean()` method instead.
 */
class BooleanColumn extends IconColumn
{
    public function isBoolean(mixed $state = null, ?Model $relatedRecord = null): bool
    {
        return true;
    }
}
