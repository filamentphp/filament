<?php

namespace Filament\Tables\Concerns;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

trait HasRecords
{
    public function getTableRecords(): Collection | LengthAwarePaginator
    {
        $query = $this->getTableQuery();

        foreach ($this->getCachedTableColumns() as $column) {
            $column->applyEagreLoading($query);
        }

        $this->applyFiltersToTableQuery($query);

        $this->applySearchToTableQuery($query);

        if ($this->isTablePaginationEnabled()) {
            return $query->paginate($this->getTableRecordsPerPage());
        } else {
            return $query->get();
        }
    }

    protected function resolveTableRecord(string $key): ?Model
    {
        return $this->getTableQuery()->find($key);
    }
}
