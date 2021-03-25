<?php

namespace Filament\Resources\Tables;

trait HasTable
{
    use \Filament\Tables\HasTable;

    public function getTable()
    {
        if ($this->table !== null) {
            return $this->table;
        }

        return $this->table = $this->table(
            Table::for($this),
        );
    }

    protected function table(Table $table)
    {
        return $table;
    }
}
