<?php

namespace Filament\Tables\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

interface HasTable
{
    public function callTableColumnAction(string $columnName, string $recordKey): void;

    public function getCachedTableColumns(): array;

    public function getTableQuery(): Builder;

    public function getTableRecords(): Collection;
}
