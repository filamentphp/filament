<?php

namespace Filament\Tables\Concerns;

trait HasPerPageAllOption
{
    protected function getPerPageAllOption(): ?bool
    {
        return false;
    }
}
