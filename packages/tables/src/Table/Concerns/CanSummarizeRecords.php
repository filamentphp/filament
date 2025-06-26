<?php

namespace Filament\Tables\Table\Concerns;

use Closure;
use Illuminate\Database\Eloquent\Builder;

trait CanSummarizeRecords
{
    public function hasSummary(Builder | Closure | null $query): bool
    {
        foreach ($this->getColumns() as $column) {
            if ($column->hasSummary($query)) {
                return true;
            }
        }

        return false;
    }
}
