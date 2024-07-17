<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;

trait CanBeSortable
{
    protected bool | Closure $isSortable = false;

    /**
     * @var array<string> | null
     */
    protected ?array $sortColumns = [];

    protected ?Closure $sortQuery = null;

    /**
     * @param  bool | array<string> | Closure  $condition
     */
    public function sortable(bool | array | Closure $condition = true, ?Closure $query = null): static
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

    /**
     * @return array<string>
     */
    public function getSortColumns(): array
    {
        return $this->sortColumns ?? $this->getDefaultSortColumns();
    }

    public function isSortable(): bool
    {
        return (bool) $this->evaluate($this->isSortable);
    }

    /**
     * @return array{0: string}
     */
    public function getDefaultSortColumns(): array
    {
        return [str($this->getName())->afterLast('.')];
    }
}
