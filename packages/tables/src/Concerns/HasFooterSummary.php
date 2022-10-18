<?php

namespace Filament\Tables\Concerns;

trait HasFooterSummary
{
    public function hasTableFooterSummary(): bool
    {
        foreach ($this->getCachedTableColumns() as $column) {
            if ($column->hasSummary()) {
                return true;
            }
        }

        return false;
    }
}
