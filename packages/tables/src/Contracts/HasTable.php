<?php

namespace Filament\Tables\Contracts;

use Filament\Forms\ComponentContainer;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

interface HasTable extends HasForms
{
    public function areAllTableRecordsOnCurrentPageSelected(): bool;

    public function areAllTableRecordsSelected(): bool;

    public function callTableColumnAction(string $columnName, string $recordKey);

    public function deselectAllTableRecords(): void;

    public function getAllTableRecordsCount(): int;

    public function getCachedTableActions(): array;

    public function getCachedTableBulkActions(): array;

    public function getCachedTableColumns(): array;

    public function getCachedTableEmptyStateActions(): array;

    public function getCachedTableFilters(): array;

    public function getCachedTableHeaderActions(): array;

    public function getFilteredTableQuery(): Builder;

    public function getMountedTableAction(): ?Action;

    public function getMountedTableActionForm(): ComponentContainer;

    public function getMountedTableBulkAction(): ?BulkAction;

    public function getMountedTableBulkActionForm(): ComponentContainer;

    public function getSelectedTableRecordCount(): int;

    public function getTableFiltersForm(): ComponentContainer;

    public function getTableRecords(): Collection | LengthAwarePaginator;

    public function getTableSortColumn(): ?string;

    public function getTableSortDirection(): ?string;

    public function isTableFilterable(): bool;

    public function isTableRecordSelected(string $record): bool;

    public function isTableSearchable(): bool;

    public function isTableSelectionEnabled(): bool;
}
