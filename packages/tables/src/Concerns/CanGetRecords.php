<?php

namespace Filament\Tables\Concerns;

trait CanGetRecords
{
    public function getRecords()
    {
        $query = static::getQuery();

        $query = $this->applyFilters($query);

        $query = $this->applySorting($query);

        if ($this->hasPagination()) {
            return $query->paginate($this->recordsPerPage);
        }

        return $query->get();
    }
}
