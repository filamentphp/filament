<?php

namespace Filament\Tables\Concerns;

use Closure;

trait HasRecordClasses
{
    protected function getTableRecordClassesUsing(): ?Closure
    {
        return null;
    }
}
