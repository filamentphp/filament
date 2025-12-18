<?php

namespace Filament\Tables\Table\Concerns;

use Closure;
use Illuminate\Database\Eloquent\Builder;

trait CanSummarizeRecords
{
    protected bool | Closure | null $hasPageSummary = null;

    protected bool | Closure | null $hasTotalSummary = null;

    public function summaries(bool | Closure | null $pageSummary = null, bool | Closure | null $totalSummary = null): static
    {
        if ($pageSummary !== null) {
            $this->hasPageSummary = $pageSummary;
        }

        if ($totalSummary !== null) {
            $this->hasTotalSummary = $totalSummary;
        }

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
        if (
            $this->hasPageSummary === false &&
            $this->hasTotalSummary === false
        ) {
            return false;
        }

        foreach ($this->getColumns() as $column) {
            if ($column->hasSummary($query)) {
                return true;
            }
        }

        return false;
    }
}
