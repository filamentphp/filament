<?php

namespace Filament\Pages\Concerns;

trait ExposesTableToWidgets /** @phpstan-ignore trait.unused */
{
    public function getWidgetData(): array
    {
        return [
            'activeTab' => $this->activeTab,
            'paginators' => $this->paginators,
            'tableRecordsCount' => $this->getAllTableRecordsCount(),
            'parentRecord' => $this->parentRecord,
            'tableColumnSearches' => $this->tableColumnSearches,
            'tableFilters' => $this->tableFilters,
            'tableGrouping' => $this->tableGrouping,
            'tableRecordsPerPage' => $this->tableRecordsPerPage,
            'tableSearch' => $this->tableSearch,
            'tableSort' => $this->tableSort,
        ];
    }
}
