<?php

namespace Filament\Tables\Concerns;

use Closure;

trait HasRecordAction
{
    protected function getTableRecordAction(): ?string
    {
        return null;
    }
}
