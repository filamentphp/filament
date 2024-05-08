<?php

namespace Filament\Tables\Table\Concerns;

use Closure;
use Filament\Tables\Columns\Column;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

trait CanSortRecords
{
    protected string | Closure | null $defaultSortColumn = null;

    protected string | Closure | null $defaultSortDirection = null;

    protected ?Closure $defaultSortQuery = null;

    protected bool | Closure | null $persistsSortInSession = false;

    public function defaultSort(string | Closure | null $column, string | Closure | null $direction = 'asc'): static
    {
        $this->defaultSortColumn = $column;

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

    public function getDefaultSortColumn(): ?string
    {
        $column = $this->evaluate($this->defaultSortColumn);

        if (is_null($column) || $column instanceof Builder) {
            $this->defaultSortQuery = $this->defaultSortColumn;

            return null;
        }

        return $column;
    }

    public function getDefaultSortDirection(): ?string
    {
        $direction = $this->evaluate($this->defaultSortDirection);

        if ($direction !== null) {
            $direction = Str::lower($direction);
        }

        return $direction;
    }

    public function getDefaultSortQuery(): ?Closure
    {
        return $this->defaultSortQuery;
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
