<?php

namespace Filament\Tables\Concerns;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

trait HasRecords
{
    public function getTableRecords(): Collection
    {
        $query = $this->getTableQuery();

        foreach ($this->getCachedTableColumns() as $column) {
            $column->applyQueryModifications($query);
        }

        return $query->get();
    }

    protected function resolveTableRecord(string $key): ?Model
    {
        return $this->getTableQuery()->find($key);
    }
}
