<?php

namespace Filament\Tables\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait CanSummarizeRecords
{
    public function getAllTableSummaryQuery(): Builder
    {
        return $this->getFilteredTableQuery();
    }

    public function getPageTableSummaryQuery(): Builder
    {
        return $this->applySortingToTableQuery(
            $this->getFilteredTableQuery()->forPage(
                page: $this->getTableRecords()->currentPage(),
                perPage: $this->getTableRecords()->perPage(),
            ),
        );
    }
}
