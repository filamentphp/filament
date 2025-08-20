<?php

namespace Filament\Tables\Table\Concerns;

use Closure;
use Illuminate\Database\Eloquent\Builder;

trait CanSummarizeRecords
{
    protected string | Closure | null $summaryHeaderLabel = null;

    public function summaryHeaderLabel(string | Closure | null $label): static
    {
        $this->summaryHeaderLabel = $label;

        return $this;
    }

    public function getSummaryHeaderLabel(): ?string
    {
        return $this->evaluate($this->summaryHeaderLabel);
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
