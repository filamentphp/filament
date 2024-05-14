<?php

namespace Filament\Tables\Table\Concerns;

use Closure;
use Filament\Tables\Columns\Column;
use Illuminate\Support\Str;

trait CanSortRecords
{
    protected string | Closure | null $defaultSort = null;

    protected string | Closure | null $defaultSortDirection = null;

    protected bool | Closure | null $persistsSortInSession = false;

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

    public function getDefaultSort(): Closure | string | null
    {
        $defaultSort = $this->evaluate($this->defaultSort);

        if (is_string($defaultSort)) {
            return $defaultSort;
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
}
