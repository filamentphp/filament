<?php

namespace Filament\Tables\Concerns;

/**
 * @deprecated Override the `table()` method to configure the table.
 */
trait CanBeStriped
{
    /**
     * @deprecated Override the `table()` method to configure the table.
     */
    protected function isTableStriped(): bool
    {
        return false;
    }
}
