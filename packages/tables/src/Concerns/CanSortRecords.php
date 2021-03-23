<?php

namespace Filament\Tables\Concerns;

use Illuminate\Support\Str;

trait CanSortRecords
{
    public $isSortable = true;

    public $sortColumn = null;

    public $sortDirection = 'asc';

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

    protected function applySorting($query)
    {
        if (
            ! $this->isSortable() ||
            $this->sortColumn === '' ||
            $this->sortColumn === null
        ) {
            return $query;
        }

        if (Str::of($this->sortColumn)->contains('.')) {
            $relationship = (string) Str::of($this->sortColumn)->beforeLast('.');

            return $query->with([
                $relationship => function ($query) {
                    return $query->orderBy(
                        (string) Str::of($this->sortColumn)->afterLast('.'),
                        $this->sortDirection,
                    );
                },
            ]);
        }

        return $query->orderBy($this->sortColumn, $this->sortDirection);
    }
}
