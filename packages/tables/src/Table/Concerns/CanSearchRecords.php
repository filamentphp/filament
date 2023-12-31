<?php

namespace Filament\Tables\Table\Concerns;

use Closure;
use Filament\Tables\Filters\Indicator;

trait CanSearchRecords
{
    protected ?bool $isSearchable = null;

    protected bool | Closure | null $persistsSearchInSession = false;

    protected bool | Closure | null $persistsColumnSearchesInSession = false;

    protected string | Closure | null $searchPlaceholder = null;

    protected ?string $searchDebounce = null;

    protected bool | Closure $isSearchOnBlur = false;

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

    public function searchable(?bool $condition = true): static
    {
        $this->isSearchable = $condition;

        return $this;
    }

    public function searchDebounce(?string $debounce): static
    {
        $this->searchDebounce = $debounce;

        return $this;
    }

    public function isSearchable(): bool
    {
        if (is_bool($this->isSearchable)) {
            return $this->isSearchable;
        }

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

    public function getSearchIndicator(): Indicator
    {
        return $this->getLivewire()->getTableSearchIndicator();
    }

    /**
     * @return array<Indicator> | array<string, string>
     */
    public function getColumnSearchIndicators(): array
    {
        return $this->getLivewire()->getTableColumnSearchIndicators();
    }

    public function getSearchDebounce(): string
    {
        return $this->searchDebounce ?? '500ms';
    }

    public function searchOnBlur(bool | Closure $condition = true): static
    {
        $this->isSearchOnBlur = $condition;

        return $this;
    }

    public function isSearchOnBlur(): bool
    {
        return (bool) $this->evaluate($this->isSearchOnBlur);
    }
}
