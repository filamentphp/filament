<?php

namespace Filament\Tables;

trait HasTable
{
    use Concerns\CanFilterRecords;
    use Concerns\CanGetRecords;
    use Concerns\CanPaginateRecords;
    use Concerns\CanReorderRecords;
    use Concerns\CanSearchRecords;
    use Concerns\CanSelectRecords;
    use Concerns\CanSortRecords;

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
