<?php

namespace Filament\Resources\Tables;

trait HasTable
{
    use \Filament\Tables\HasTable;

    protected function table(Table $table)
    {
        return $table;
    }

    public function getTable()
    {
        if ($this->table !== null) {
            return $this->table;
        }

        return $this->table = $this->table(
            Table::for($this),
        );
    }
}
