<?php

namespace Filament\Pages\Concerns;

trait ExposesTableToWidgets /** @phpstan-ignore trait.unused */
{
    public function getWidgetData(): array
    {
        return [
            'activeTab' => $this->activeTab,
            'paginators' => $this->paginators,
            'parentRecord' => $this->parentRecord,
            'tableColumnSearches' => $this->tableColumnSearches,
            'tableFilters' => $this->tableFilters,
            'tableGrouping' => $this->tableGrouping,
            'tableGroupingDirection' => $this->tableGroupingDirection,
            'tableRecordsPerPage' => $this->tableRecordsPerPage,
            'tableSearch' => $this->tableSearch,
            'tableSortColumn' => $this->tableSortColumn,
            'tableSortDirection' => $this->tableSortDirection,
        ];
    }
}
