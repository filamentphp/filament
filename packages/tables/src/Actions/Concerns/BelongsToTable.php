<?php

namespace Filament\Tables\Actions\Concerns;

use Exception;
use Filament\Tables\Actions\Contracts\HasTable as ActionHasTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

trait BelongsToTable
{
    protected Table $table;

    public function table(Table $table): static
    {
        $this->table = $table;

        return $this;
    }

    public function getTable(): Table
    {
        if (isset($this->table)) {
            return $this->table;
        }

        $group = $this->getGroup();

        if (! ($group instanceof ActionHasTable)) {
            throw new Exception('This action does not belong to a table.');
        }

        return $group->getTable();
    }

    public function getLivewire(): HasTable
    {
        return $this->getTable()->getLivewire();
    }
}
