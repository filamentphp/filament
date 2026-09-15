<?php

namespace Filament\Tables\Components\Concerns;

use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use LogicException;

trait BelongsToTable
{
    public function getTable(): Table
    {
        $livewire = $this->getLivewire();

        if (! ($livewire instanceof HasTable)) {
            throw new LogicException('The [' . static::class . '] table component can only be used inside a Livewire component that implements [' . HasTable::class . ']. Place it inside `$table->layout()`.');
        }

        return $livewire->getTable();
    }
}
