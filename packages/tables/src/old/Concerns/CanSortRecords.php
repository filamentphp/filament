<?php

namespace Filament\Tables\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

trait CanSortRecords
{
    public ?string $defaultSortColumn = null;

    public ?string $defaultSortDirection = null;

    public $isSortable = true;

    public ?string $sortColumn = null;

    public string $sortDirection = 'asc';

    public function getDefaultSort(): array
    {
        return [$this->getDefaultSortColumn(), $this->getDefaultSortDirection()];
    }

    public function getDefaultSortColumn(): ?string
    {
        return $this->defaultSortColumn ?? $this->getTable()->getDefaultSortColumn();
    }

    public function getDefaultSortDirection(): ?string
    {
        return $this->defaultSortDirection ?? $this->getTable()->getDefaultSortDirection();
    }

    public function getSorts(): array
    {
        $sortColumn = $this->sortColumn;
        $sortDirection = $this->sortDirection;

        if (
            ! $this->isSortable() ||
            $sortColumn === '' ||
            $sortColumn === null
        ) {
            if (! $this->hasDefaultSort()) {
                return [];
            }

            return [
                $this->getDefaultSort(),
            ];
        }

        $column = collect($this->getTable()->getColumns())
            ->filter(fn ($column) => $column->getName() === $sortColumn)
            ->first();

        if ($column === null) {
            return [];
        }

        return collect($column->getSortColumns())
            ->map(fn ($sortColumn) => [$sortColumn, $sortDirection])
            ->toArray();
    }

    public function hasDefaultSort(): bool
    {
        return $this->getDefaultSortColumn() !== null;
    }

    public function isSortable(): bool
    {
        return $this->isSortable && collect($this->getTable()->getColumns())
            ->filter(fn ($column) => $column->isSortable())
            ->count();
    }

    public function sortBy(string $column): void
    {
        if ($this->sortColumn === $column) {
            switch ($this->sortDirection) {
                case 'asc':
                    $this->sortDirection = 'desc';

                    break;
                case 'desc':
                    $this->sortColumn = null;
                    $this->sortDirection = 'asc';

                    break;
            }

            return;
        }

        $this->sortColumn = $column;
        $this->sortDirection = 'asc';
    }

    protected function applyRelationshipSort(Builder $query, array $sort): Builder
    {
        [$sortColumn, $sortDirection] = $sort;

        $parentModel = $query->getModel();
        $relationshipName = (string) Str::of($sortColumn)->beforeLast('.');
        $relationship = $parentModel->{$relationshipName}();
        $relatedColumnName = (string) Str::of($sortColumn)->afterLast('.');
        $relatedModel = $relationship->getModel();

        return $query->orderBy(
            $relatedModel
                ->query()
                ->select($relatedColumnName)
                ->whereColumn(
                    "{$relatedModel->getTable()}.{$relationship->getOwnerKeyName()}",
                    "{$parentModel->getTable()}.{$relationship->getForeignKeyName()}",
                ),
            $sortDirection,
        );
    }

    protected function applySorting(Builder $query): Builder
    {
        foreach ($this->getSorts() as $sort) {
            [$column, $direction] = $sort;

            if ($this->isRelationshipSort($column)) {
                $query = $this->applyRelationshipSort(
                    $query,
                    [$column, $direction],
                );
            } else {
                $query = $query->orderBy($column, $direction);
            }
        }

        return $query;
    }

    protected function isRelationshipSort($column): bool
    {
        return Str::of($column)->contains('.');
    }
}
