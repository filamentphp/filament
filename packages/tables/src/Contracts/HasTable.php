<?php

namespace Filament\Tables\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

interface HasTable
{
    public function areAllRecordsOnCurrentTablePageSelected(): bool;

    public function callTableColumnAction(string $columnName, string $recordKey): void;

    public function getCachedTableActions(): array;

    public function getCachedTableBulkActions(): array;

    public function getCachedTableColumns(): array;

    public function getCachedTableFilters(): array;

    public function getTableEmptyStateDescription(): ?string;

    public function getTableEmptyStateHeading(): string;

    public function getTableEmptyStateIcon(): string;

    public function getTableEmptyStateView(): ?View;

    public function getTableQuery(): Builder;

    public function getTableRecords(): Collection | LengthAwarePaginator;

    public function isTableFilterable(): bool;

    public function isTablePaginationEnabled(): bool;

    public function isTableRecordSelected(string $record): bool;

    public function isTableSearchable(): bool;

    public function isTableSelectionEnabled(): bool;
}
