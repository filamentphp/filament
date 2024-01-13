<?php

namespace Filament\Tables\Contracts;

use Filament\Forms\Form;
use Filament\Support\Contracts\TranslatableContentDriver;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface HasTable
{
    public function callTableColumnAction(string $name, string $recordKey): mixed;

    public function deselectAllTableRecords(): void;

    public function getActiveTableLocale(): ?string;

    /**
     * @return array<int | string>
     */
    public function getAllSelectableTableRecordKeys(): array;

    public function getAllTableRecordsCount(): int;

    public function getAllSelectableTableRecordsCount(): int;

    /**
     * @return array<string, mixed> | null
     */
    public function getTableFilterState(string $name): ?array;

    public function getSelectedTableRecords(bool $shouldFetchSelectedRecords = true): EloquentCollection | Collection;

    public function parseTableFilterName(string $name): string;

    public function getTableGrouping(): ?Group;

    public function getMountedTableAction(): ?Action;

    public function getMountedTableActionForm(): ?Form;

    public function getMountedTableActionRecord(): ?Model;

    public function getMountedTableActionRecordKey(): int | string | null;

    public function getMountedTableBulkAction(): ?BulkAction;

    public function getMountedTableBulkActionForm(): ?Form;

    public function getTable(): Table;

    public function getTableFiltersForm(): Form;

    public function getTableRecords(): EloquentCollection | Paginator | CursorPaginator;

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

    public function hasTableSearch(): bool;

    public function resetTableSearch(): void;

    public function resetTableColumnSearch(string $column): void;

    public function getTableSearchIndicator(): Indicator;

    /**
     * @return array<Indicator>
     */
    public function getTableColumnSearchIndicators(): array;

    public function getFilteredTableQuery(): Builder;

    public function getFilteredSortedTableQuery(): Builder;

    public function makeFilamentTranslatableContentDriver(): ?TranslatableContentDriver;

    /**
     * @param  array<string, mixed>  $arguments
     */
    public function callMountedTableAction(array $arguments = []): mixed;

    /**
     * @param  array<string, mixed>  $arguments
     */
    public function mountTableAction(string $name, ?string $record = null, array $arguments = []): mixed;

    /**
     * @param  array<string, mixed>  $arguments
     */
    public function replaceMountedTableAction(string $name, ?string $record = null, array $arguments = []): void;

    /**
     * @param  array<int | string> | null  $selectedRecords
     */
    public function mountTableBulkAction(string $name, ?array $selectedRecords = null): mixed;

    /**
     * @param  array<int | string> | null  $selectedRecords
     */
    public function replaceMountedTableBulkAction(string $name, ?array $selectedRecords = null): void;
}
