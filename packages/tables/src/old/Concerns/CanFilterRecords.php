<?php

namespace Filament\Tables\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait CanFilterRecords
{
    public ?string $filter = null;

    protected bool $isFilterable = true;

    public function isFilterable(): bool
    {
        return $this->isFilterable && count($this->getTable()->getFilters());
    }

    public function updatedFilter(): void
    {
        $this->selected = [];

        if (! $this->hasPagination()) {
            return;
        }

        $this->resetPage();
    }

    protected function applyFilters(Builder $query): Builder
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
