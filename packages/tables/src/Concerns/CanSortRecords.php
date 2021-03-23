<?php

namespace Filament\Tables\Concerns;

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
}
