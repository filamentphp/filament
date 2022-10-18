<?php

namespace Filament\Tables\Concerns;

use Closure;

/**
 * @deprecated Override the `table()` method to configure the table.
 */
trait HasRecordClasses
{
    /**
     * @deprecated Override the `table()` method to configure the table.
     */
    protected function getTableRecordClassesUsing(): ?Closure
    {
        return null;
    }
}
