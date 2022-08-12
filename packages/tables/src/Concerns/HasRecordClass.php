<?php

namespace Filament\Tables\Concerns;

use Closure;

trait HasRecordClass
{
    protected function getTableRecordClassUsing(): ?Closure
    {
        return null;
    }
}
