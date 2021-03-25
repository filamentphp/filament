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

    protected $table;

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
