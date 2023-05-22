<?php

namespace Filament\Tables\Contracts;

use Filament\Forms\ComponentContainer;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\Layout\Component;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface HasTable extends HasForms
{
    public function allowsDuplicates(): bool;

    public function callTableColumnAction(string $columnName, string $recordKey);

    public function deselectAllTableRecords(): void;

    public function getActiveTableLocale(): ?string;

    public function getAllSelectableTableRecordKeys(): array;

    public function getAllSelectableTableRecordsCount(): int;

    public function getAllTableRecordsCount(): int;

    public function getCachedTableActions(): array;

    public function getCachedTableBulkActions(): array;

    public function getCachedTableColumns(): array;

    public function getCachedTableColumnsLayout(): array;

    public function getCachedCollapsibleTableColumnsLayout(): ?Component;

    public function hasTableColumnsLayout(): bool;

    public function getCachedTableEmptyStateActions(): array;

    public function getCachedTableFilters(): array;

    public function getCachedTableHeaderActions(): array;

    public function getTableFilterState(string $name): ?array;

    public function parseFilterName(string $name): string;

    public function getMountedTableAction(): ?Action;

    public function getMountedTableActionForm(): ?ComponentContainer;

    public function getMountedTableActionRecordKey();

    public function getMountedTableBulkAction(): ?BulkAction;

    public function getMountedTableBulkActionForm(): ?ComponentContainer;

    public function getTableFiltersForm(): ComponentContainer;

    public function getTableModel(): string;

    public function getTableRecords(): Collection | Paginator;

    public function getTableSortColumn(): ?string;

    public function getTableSortDirection(): ?string;

    public function isTableFilterable(): bool;

    public function isTableSearchable(): bool;

    public function isTableSearchableByColumn(): bool;

    public function isTableSelectionEnabled(): bool;

    public function getTableRecordCheckboxPosition(): string;

    public function hasToggleableTableColumns(): bool;

    public function isTableColumnToggledHidden(string $name): bool;

    public function getTableColumnToggleForm(): ComponentContainer;

    public function getTableRecord(?string $key): ?Model;

    public function getTableRecordKey(Model $record): string;

    public function getTableRecordTitle(Model $record): string;

    public function getTablePluralModelLabel(): string;

    public function getTableModelLabel(): string;

    public function mountedTableActionRecord($record): void;

    public function toggleTableReordering(): void;

    public function isTableReordering(): bool;

    public function isTableLoaded(): bool;

    public function hasTableColumnSearches(): bool;
}
