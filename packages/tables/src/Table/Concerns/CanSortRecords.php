<?php

namespace Filament\Tables\Table\Concerns;

use Closure;
use Filament\Tables\Columns\Column;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

trait CanSortRecords
{
    protected string | Closure | null $defaultSort = null;

    protected string | Closure | null $defaultSortDirection = null;

    protected bool | Closure | null $persistsSortInSession = false;

    protected string | Htmlable | Closure | null $defaultSortOptionLabel = null;

    public function defaultSort(string | Closure | null $column, string | Closure | null $direction = 'asc'): static
    {
        $this->defaultSort = $column;
        $this->defaultSortDirection = $direction;

        return $this;
    }

    public function persistSortInSession(bool | Closure $condition = true): static
    {
        $this->persistsSortInSession = $condition;

        return $this;
    }

    public function defaultSortOptionLabel(string | Htmlable | Closure | null $label): static
    {
        $this->defaultSortOptionLabel = $label;

        return $this;
    }

    public function getSortableVisibleColumn(string $name): ?Column
    {
        $column = $this->getColumn($name);

        if (! $column) {
            return null;
        }

        if ($column->isHidden()) {
            return null;
        }

        if (! $column->isSortable()) {
            return null;
        }

        return $column;
    }

    public function getDefaultSort(Builder $query, string $direction): string | Builder | null
    {
        return $this->evaluate($this->defaultSort, [
            'direction' => $direction,
            'query' => $query,
        ]);
    }

    /**
     * @deprecated Use `getDefaultSort()` instead.
     */
    public function getDefaultSortColumn(): ?string
    {
        if (! is_string($this->defaultSort)) {
            return null;
        }

        return $this->defaultSort;
    }

    /**
     * @deprecated Use `getDefaultSort()` instead.
     */
    public function getDefaultSortQuery(): ?Closure
    {
        if (! ($this->defaultSort instanceof Closure)) {
            return null;
        }

        return $this->defaultSort;
    }

    public function getDefaultSortDirection(): ?string
    {
        $direction = $this->evaluate($this->defaultSortDirection);

        if ($direction !== null) {
            $direction = Str::lower($direction);
        }

        return $direction;
    }

    public function getSortColumn(): ?string
    {
        return $this->getLivewire()->getTableSortColumn();
    }

    public function getSortDirection(): ?string
    {
        return $this->getLivewire()->getTableSortDirection();
    }

    public function persistsSortInSession(): bool
    {
        return (bool) $this->evaluate($this->persistsSortInSession);
    }

    public function getDefaultSortOptionLabel(): string | Htmlable | null
    {
        return $this->evaluate($this->defaultSortOptionLabel) ?? '-';
    }
}
