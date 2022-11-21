<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;

trait CanBeSortable
{
    protected bool $isSortable = false;

    protected ?array $sortColumns = [];

    protected ?Closure $sortQuery = null;

    public function sortable(bool | array $condition = true, ?Closure $query = null): static
    {
        if (is_array($condition)) {
            $this->isSortable = true;
            $this->sortColumns = $condition;
        } else {
            $this->isSortable = $condition;
            $this->sortColumns = null;
        }

        $this->sortQuery = $query;

        return $this;
    }

    public function getSortColumns(): array
    {
        return $this->sortColumns ?? $this->getDefaultSortColumns();
    }

    public function isSortable(): bool
    {
        return $this->isSortable;
    }

    public function getDefaultSortColumns(): array
    {
        return [str($this->getName())];
    }
}
