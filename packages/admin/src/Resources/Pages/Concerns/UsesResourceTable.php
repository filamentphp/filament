<?php

namespace Filament\Resources\Pages\Concerns;

use Filament\Resources\Table;

trait UsesResourceTable
{
    protected ?Table $resourceTable = null;

    protected function getResourceTable(): Table
    {
        if (! $this->resourceTable) {
            $this->resourceTable = static::getResource()::table(Table::make($this));
        }

        return $this->resourceTable;
    }
}
