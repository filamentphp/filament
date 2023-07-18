<?php

namespace Filament\Tables\Concerns;

/**
 * @deprecated Override the `table()` method to configure the table.
 */
trait CanPollRecords
{
    /**
     * @deprecated Override the `table()` method to configure the table.
     */
    protected function getTablePollingInterval(): ?string
    {
        return null;
    }
}
