<?php

namespace Filament\Tables\Concerns;

use Closure;
use Illuminate\Database\Eloquent\Model;

trait HasRecordAction
{
    protected function getTableRecordActionUsing(): ?Closure
    {
        return function (Model $record): ?string {
            return $this->getTableRecordAction();
        };
    }

    /**
     * @deprecated Use `getTableRecordActionUsing()` instead.
     */
    protected function getTableRecordAction(): ?string
    {
        return null;
    }
}
