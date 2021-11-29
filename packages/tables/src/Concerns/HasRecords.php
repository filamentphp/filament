<?php

namespace Filament\Tables\Concerns;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

trait HasRecords
{
    protected Collection | LengthAwarePaginator | null $records = null;

    public function getFilteredTableQuery(): Builder
    {
        $query = $this->getTableQuery();

        $this->applyFiltersToTableQuery($query);

        $this->applySearchToTableQuery($query);

        return $query;
    }

    public function getTableRecords(): Collection | LengthAwarePaginator
    {
        if ($this->records) {
            return $this->records;
        }

        $query = $this->getFilteredTableQuery();

        foreach ($this->getCachedTableColumns() as $column) {
            $column->applyEagreLoading($query);
        }

        $this->applySortingToTableQuery($query);

        $this->applySelectToTableQuery($query);

        if ($this->isTablePaginationEnabled()) {
            return $this->records = $query->paginate($this->getTableRecordsPerPage())->onEachSide(1);
        } else {
            return $this->records = $query->get();
        }
    }

    protected function resolveTableRecord(?string $key): ?Model
    {
        return $this->getTableQuery()->find($key);
    }

    protected function applySelectToTableQuery(Builder $query): Builder
    {
        return $query;
    }
}
