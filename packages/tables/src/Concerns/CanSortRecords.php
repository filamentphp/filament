<?php

namespace Filament\Tables\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait CanSortRecords
{
    public $tableSortColumns = [];

    public function sortTable(?string $column = null): void
    {
        if (collect($this->tableSortColumns)->contains(fn ($pair) => $pair['column'] === $column)) {
            $direction = match (collect($this->tableSortColumns)->filter(fn ($pair) => $pair['column'] === $column)->pluck('direction')->first()) {
                'asc' => 'desc',
                'desc' => null,
                default => 'asc',
            };
        } else {
            $direction = 'asc';
        }


        $this->tableSortColumns = [
            'column' => $direction ? $column : null,
            'direction' => $direction,
        ];

        $this->updatedTableSort();
    }

    public function getTableSortColumns(): ?array
    {
        return $this->tableSortColumns;
    }

    protected function getDefaultTableSortColumns(): ?array
    {
        return null;
    }

    public function updatedTableSort(): void
    {
        $this->resetPage();
    }

    protected function applySortingToTableQuery(Builder $query): Builder
    {
        foreach ($this->tableSortColumns as $pair) {
            [$columnName, $direction] = $pair;

            if (! $columnName) {
                continue;
            }

            $direction = $direction === 'desc' ? 'desc' : 'asc';

            if ($column = $this->getCachedTableColumn($columnName)) {
                $column->applySort($query, $direction);

                continue;
            }

            if ($columnName === $this->getDefaultTableSortColumns()) {
                $query->orderBy($columnName, $direction);
            }
        }

        return $query;
    }
}
