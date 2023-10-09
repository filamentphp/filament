<?php

namespace Filament\Tables\Table\Concerns;

use Closure;

trait CanSearchRecords
{
    protected bool | Closure | null $persistsSearchInSession = false;

    protected bool | Closure | null $persistsColumnSearchesInSession = false;

    protected string | Closure | null $searchPlaceholder = null;

    public function persistSearchInSession(bool | Closure $condition = true): static
    {
        $this->persistsSearchInSession = $condition;

        return $this;
    }

    public function persistColumnSearchesInSession(bool | Closure $condition = true): static
    {
        $this->persistsColumnSearchesInSession = $condition;

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

    public function persistsColumnSearchesInSession(): bool
    {
        return (bool) $this->evaluate($this->persistsColumnSearchesInSession);
    }

    public function searchPlaceholder(string | Closure | null $searchPlaceholder): static
    {
        $this->searchPlaceholder = $searchPlaceholder;

        return $this;
    }

    public function getSearchPlaceholder(): ?string
    {
        return $this->evaluate($this->searchPlaceholder);
    }

    public function hasSearch(): bool
    {
        return $this->getLivewire()->hasTableSearch();
    }

    public function getSearchIndicator(): string
    {
        return $this->getLivewire()->getTableSearchIndicator();
    }

    /**
     * @return array<string, string>
     */
    public function getColumnSearchIndicators(): array
    {
        return $this->getLivewire()->getTableColumnSearchIndicators();
    }
}
