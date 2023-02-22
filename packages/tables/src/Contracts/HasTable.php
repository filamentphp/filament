<?php

namespace Filament\Tables\Contracts;

use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface HasTable extends HasForms
{
    public function callTableColumnAction(string $name, string $recordKey): mixed;

    public function deselectAllTableRecords(): void;

    public function getActiveTableLocale(): ?string;

    /**
     * @return array<int | string>
     */
    public function getAllTableRecordKeys(): array;

    public function getAllTableRecordsCount(): int;

    /**
     * @return array<string, mixed> | null
     */
    public function getTableFilterState(string $name): ?array;

    public function getSelectedTableRecords(): Collection;

    public function parseFilterName(string $name): string;

    public function getTableGrouping(): ?Group;

    public function getMountedTableAction(): ?Action;

    public function getMountedTableActionForm(): ?Form;

    public function getMountedTableActionRecord(): ?Model;

    public function getMountedTableActionRecordKey(): int | string | null;

    public function getMountedTableBulkAction(): ?BulkAction;

    public function getMountedTableBulkActionForm(): ?Form;

    public function getTable(): Table;

    public function getTableFiltersForm(): Form;

    public function getTableRecords(): Collection | Paginator;

    public function getTableRecordsPerPage(): int | string | null;

    public function getTablePage(): int;

    public function getTableSortColumn(): ?string;

    public function getTableSortDirection(): ?string;

    public function getAllTableSummaryQuery(): Builder;

    public function getPageTableSummaryQuery(): Builder;

    public function isTableColumnToggledHidden(string $name): bool;

    public function getTableColumnToggleForm(): Form;

    public function getTableRecord(?string $key): ?Model;

    public function getTableRecordKey(Model $record): string;

    public function mountedTableActionRecord(int | string | null $record): void;

    public function toggleTableReordering(): void;

    public function isTableReordering(): bool;

    public function isTableLoaded(): bool;
}
