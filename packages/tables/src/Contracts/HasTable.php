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
    /**
     * @return mixed
     */
    public function callTableColumnAction(string $name, string $recordKey);

    public function deselectAllTableRecords(): void;

    public function getActiveTableLocale(): ?string;

    /**
     * @return array<scalar>
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

    /**
     * @return scalar
     */
    public function getMountedTableActionRecordKey();

    public function getMountedTableBulkAction(): ?BulkAction;

    public function getMountedTableBulkActionForm(): ?Form;

    public function getTable(): Table;

    public function getTableFiltersForm(): Form;

    public function getTableRecords(): Collection | Paginator;

    public function getTableSortColumn(): ?string;

    public function getTableSortDirection(): ?string;

    public function getAllTableSummaryQuery(): Builder;

    public function getPageTableSummaryQuery(): Builder;

    public function isTableColumnToggledHidden(string $name): bool;

    public function getTableColumnToggleForm(): Form;

    public function getTableRecord(?string $key): ?Model;

    public function getTableRecordKey(Model $record): string;

    /**
     * @param  scalar  $record
     */
    public function mountedTableActionRecord($record): void;

    public function toggleTableReordering(): void;

    public function isTableReordering(): bool;
}
