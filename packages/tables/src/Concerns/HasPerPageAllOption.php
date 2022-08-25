<?php

namespace Filament\Tables\Concerns;

use Closure;

trait HasPerPageAllOption
{
    protected function getPerPageAllOption(): ?bool
    {
        return false;
    }
}
