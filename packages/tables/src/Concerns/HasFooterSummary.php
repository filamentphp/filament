<?php

namespace Filament\Tables\Concerns;

trait HasFooterSummary
{
    public function hasTableFooterSummary(): bool
    {
        foreach ($this->getCachedTableColumns() as $column) {
            if (method_exists($column, 'hasSummary') && $column->hasSummary()) {
                return true;
            }
        }

        return false;
    }
}
