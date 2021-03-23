<?php

namespace Filament\Tables\Concerns;

use Illuminate\Support\Str;

trait CanSortRecords
{
    public $isSortable = true;

    public $sortColumn;

    public $sortDirection = 'asc';

    public function getDefaultSortColumn()
    {
        return null;
    }

    public function getDefaultSortDirection()
    {
        return 'asc';
    }

    public function isSortable()
    {
        return $this->isSortable && collect($this->getTable()->getColumns())
                ->filter(fn ($column) => $column->isSortable())
                ->count();
    }

    public function sortBy($column)
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

    protected function applyRelationshipSort($query, $sort)
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

    protected function applySorting($query)
    {
        $sortColumn = $this->sortColumn;
        $sortDirection = $this->sortDirection;

        if (
            ! $this->isSortable() ||
            $sortColumn === '' ||
            $sortColumn === null
        ) {
            if ($this->getDefaultSortColumn() === null) {
                return $query;
            }

            $sortColumn = $this->getDefaultSortColumn();
            $sortDirection = $this->getDefaultSortDirection();
        }

        if ($this->isRelationshipSort($sortColumn)) {
            return $this->applyRelationshipSort(
                $query,
                [$sortColumn, $sortDirection],
            );
        }

        return $query->orderBy($sortColumn, $sortDirection);
    }

    protected function isRelationshipSort($column)
    {
        return Str::of($column)->contains('.');
    }
}
