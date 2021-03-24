<?php

namespace Filament\Tables\Concerns;

trait CanFilterRecords
{
    public $filter = null;

    public $isFilterable = true;

    public function isFilterable()
    {
        return $this->isFilterable && count($this->getTable()->getFilters());
    }

    public function updatedFilter()
    {
        $this->selected = [];

        if (! $this->hasPagination()) {
            return;
        }

        $this->resetPage();
    }

    protected function applyFilters($query)
    {
        if (
            ! $this->isFilterable() ||
            $this->filter === '' ||
            $this->filter === null
        ) {
            return $query;
        }

        collect($this->getTable()->getFilters())
            ->filter(fn ($filter) => $filter->getName() === $this->filter)
            ->each(function ($filter) use (&$query) {
                $query = $filter->apply($query);
            });

        return $query;
    }
}
