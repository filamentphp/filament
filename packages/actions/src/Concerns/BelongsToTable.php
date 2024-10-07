<?php

namespace Filament\Actions\Concerns;

use Filament\Tables\Table;

trait BelongsToTable
{
    protected ?Table $table = null;

    public function table(?Table $table): static
    {
        $this->table = $table;

        return $this;
    }

    public function getTable(): ?Table
    {
        if (isset($this->table)) {
            return $this->table;
        }

        return $this->getGroup()?->getTable();
    }
}
