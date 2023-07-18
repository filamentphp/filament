<?php

namespace Filament\Pages\Concerns;

trait ExposesTableToWidgets
{
    public function getWidgetData(): array
    {
        return [
            'activeTab' => $this->activeTab,
            'paginators' => $this->paginators,
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
