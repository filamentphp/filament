<?php

namespace Filament\Tables\Concerns;

use Illuminate\Support\Str;

trait CanGetRecords
{
    public function getRecords()
    {
        $query = static::getQuery();

        $query = $this->loadRelationships($query);

        $query = $this->applyFilters($query);

        $query = $this->applySearch($query);

        $query = $this->applySorting($query);

        if ($this->hasPagination()) {
            return $query->paginate($this->recordsPerPage);
        }

        return $query->get();
    }

    protected function loadRelationships($query)
    {
        $relationships = collect($this->getTable()->getColumns())
            ->filter(fn ($column) => Str::of($column->getName())->contains('.'))
            ->map(fn ($column) => (string) Str::of($column->getName())->beforeLast('.'))
            ->toArray();

        return $query->with($relationships);
    }
}
