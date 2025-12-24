<?php

namespace Filament\Tables\Table\Concerns;

use Closure;
use Illuminate\Database\Eloquent\Builder;

trait CanSummarizeRecords
{
    protected bool | Closure | null $hasPageSummary = null;

    protected bool | Closure | null $hasTotalSummary = null;

    public function summaries(bool | Closure $pageSummaryCondition = true, bool | Closure $totalSummaryCondition = true): static
    {
        $this->hasPageSummary = $pageSummaryCondition;
        $this->hasTotalSummary = $totalSummaryCondition;

        return $this;
    }

    public function hasPageSummary(): bool
    {
        return (bool) $this->evaluate($this->hasPageSummary ?? true);
    }

    public function hasTotalSummary(): bool
    {
        return (bool) $this->evaluate($this->hasTotalSummary ?? true);
    }

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
