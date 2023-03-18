<?php

namespace Filament\Tables\Table\Concerns;

use Closure;

trait CanSearchRecords
{
    protected bool | Closure | null $persistsSearchInSession = false;

    protected bool | Closure | null $persistsColumnSearchInSession = false;

    public function persistSearchInSession(bool | Closure $condition = true): static
    {
        $this->persistsSearchInSession = $condition;

        return $this;
    }

    public function persistColumnSearchInSession(bool | Closure $condition = true): static
    {
        $this->persistsColumnSearchInSession = $condition;

        return $this;
    }

    public function isSearchable(): bool
    {
        foreach ($this->getColumns() as $column) {
            if (! $column->isGloballySearchable()) {
                continue;
            }

            return true;
        }

        return false;
    }

    public function isSearchableByColumn(): bool
    {
        foreach ($this->getColumns() as $column) {
            if (! $column->isIndividuallySearchable()) {
                continue;
            }

            return true;
        }

        return false;
    }

    public function persistsSearchInSession(): bool
    {
        return (bool) $this->evaluate($this->persistsSearchInSession);
    }

    public function persistsColumnSearchInSession(): bool
    {
        return (bool) $this->evaluate($this->persistsColumnSearchInSession);
    }

    public function hasColumnSearches(): bool
    {
        return $this->getLivewire()->hasTableColumnSearches();
    }
}
