<?php

namespace Filament\Tables\Columns\Summarizers\Concerns;

use Filament\Tables\Columns\Column;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

trait BelongsToColumn
{
    protected Column $column;

    public function column(Column $column): static
    {
        $this->column = $column;

        return $this;
    }

    public function getColumn(): Column
    {
        return $this->column;
    }

    public function getTable(): Table
    {
        return $this->getColumn()->getTable();
    }

    public function getLivewire(): HasTable
    {
        return $this->getTable()->getLivewire();
    }
}
