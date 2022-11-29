<?php

namespace Filament\Tables\Concerns;

use Illuminate\Contracts\View\View;

/**
 * @deprecated Override the `table()` method to configure the table.
 */
trait HasContent
{
    /**
     * @deprecated Override the `table()` method to configure the table.
     */
    protected function getTableContent(): ?View
    {
        return null;
    }

    /**
     * @deprecated Override the `table()` method to configure the table.
     *
     * @return array<string, int | null> | null
     */
    protected function getTableContentGrid(): ?array
    {
        return null;
    }

    /**
     * @deprecated Override the `table()` method to configure the table.
     */
    protected function getTableContentFooter(): ?View
    {
        return null;
    }
}
