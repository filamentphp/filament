<?php

namespace Filament\Tables\Concerns;

trait CanBeStriped
{
    protected function isTableStriped(): bool
    {
        return false;
    }
}
