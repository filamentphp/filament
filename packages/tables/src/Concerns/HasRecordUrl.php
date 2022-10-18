<?php

namespace Filament\Tables\Concerns;

use Closure;

/**
 * @deprecated Override the `table()` method to configure the table.
 */
trait HasRecordUrl
{
    /**
     * @deprecated Override the `table()` method to configure the table.
     */
    protected function getTableRecordUrlUsing(): ?Closure
    {
        return null;
    }
}
