<?php

namespace Filament\Tables\Columns\Concerns;

use Illuminate\Support\Str;

trait CanBeSortable
{
    protected bool $isSortable = false;

    protected ?array $sortColumns = [];

    public function sortable(bool | array $condition = true): static
    {
        if (is_array($condition)) {
            $this->isSortable = true;
            $this->sortColumns = $condition;
        } else {
            $this->isSortable = $condition;
            $this->sortColumns = null;
        }

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

    protected function getDefaultSortColumns(): array
    {
        return [Str::of($this->getName())->afterLast('.')];
    }
}
