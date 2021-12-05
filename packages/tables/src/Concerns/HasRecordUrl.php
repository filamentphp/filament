<?php

namespace Filament\Tables\Concerns;

use Closure;

trait HasRecordUrl
{
    protected function getTableRecordUrlUsing(): ?Closure
    {
        return null;
    }
}
