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
        return function (Model $record): ?string {
            return $this->getTableRecordAction();
        };
    }

    /**
     * @deprecated Override the `table()` method to configure the table.
     */
    protected function getTableRecordAction(): ?string
    {
        return null;
    }
}
