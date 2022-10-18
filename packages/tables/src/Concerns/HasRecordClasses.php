<?php

namespace Filament\Tables\Concerns;

use Closure;

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
