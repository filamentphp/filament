<?php

namespace Filament\Tables\Table\Concerns;

use Closure;
use Illuminate\Database\Eloquent\Builder;

trait CanSummarizeRecords
{
    protected bool | Closure $hasPageSummary = true;

    protected bool | Closure $hasAllTableSummary = true;

    public function summaries(bool | Closure $pageCondition = true, bool | Closure $allTableCondition = true): static
    {
        $this->hasPageSummary = $pageCondition;
        $this->hasAllTableSummary = $allTableCondition;

        return $this;
    }

    public function hasPageSummary(): bool
    {
        return (bool) $this->evaluate($this->hasPageSummary);
    }

    public function hasAllTableSummary(): bool
    {
        return (bool) $this->evaluate($this->hasAllTableSummary);
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
