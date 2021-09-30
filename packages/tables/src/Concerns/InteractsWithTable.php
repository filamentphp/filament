<?php

namespace Filament\Tables\Concerns;

use Filament\Tables\Table;

trait InteractsWithTable
{
    use CanFilterRecords;
    use CanGetRecords;
    use CanPaginateRecords;
    use CanReorderRecords;
    use CanRunBulkRecordActions;
    use CanSearchRecords;
    use CanSelectRecords;
    use CanSortRecords;

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
