<?php

namespace Filament\Tables\Concerns;

use Closure;

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
