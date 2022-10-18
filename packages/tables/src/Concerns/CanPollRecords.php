<?php

namespace Filament\Tables\Concerns;

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
