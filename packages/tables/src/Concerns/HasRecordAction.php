<?php

namespace Filament\Tables\Concerns;

/**
 * @deprecated Override the `table()` method to configure the table.
 */
trait HasRecordAction
{
    /**
     * @deprecated Override the `table()` method to configure the table.
     */
    protected function getTableRecordAction(): ?string
    {
        return null;
    }
}
