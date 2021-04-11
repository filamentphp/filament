<?php

namespace Filament\Resources\Tables;

trait HasTable
{
    use \Filament\Tables\HasTable;

    public function getTable()
    {
        return $this->table(
            Table::for($this),
        );
    }

    protected function table(Table $table)
    {
        return $table;
    }
}
