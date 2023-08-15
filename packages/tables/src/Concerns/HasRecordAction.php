<?php

namespace Filament\Tables\Concerns;

use Closure;
use Illuminate\Database\Eloquent\Model;

/**
 * @deprecated Override the `table()` method to configure the table.
 */
trait HasRecordAction
{
    /**
     * @deprecated Override the `table()` method to configure the table.
     */
    protected function getTableRecordActionUsing(): ?Closure
    {
        return fn (Model $record): ?string => $this->getTableRecordAction();
    }

    /**
     * @deprecated Override the `table()` method to configure the table.
     */
    protected function getTableRecordAction(): ?string
    {
        return null;
    }
}
