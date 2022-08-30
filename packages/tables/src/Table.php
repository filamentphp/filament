<?php

namespace Filament\Tables;

use Filament\Forms\ComponentContainer;
use Filament\Support\Components\ViewComponent;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\Position;
use Filament\Tables\Columns\Column;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Layout;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class Table extends ViewComponent
{
    use Concerns\BelongsToLivewire;

    protected string $view = 'tables::index';

    protected string $viewIdentifier = 'table';

    public const LOADING_TARGETS = ['previousPage', 'nextPage', 'gotoPage', 'sortTable', 'tableFilters', 'resetTableFiltersForm', 'tableSearchQuery', 'tableColumnSearchQueries', 'tableRecordsPerPage', '$set'];

    final public function __construct(HasTable $livewire)
    {
        $this->livewire($livewire);
    }

    public static function make(HasTable $livewire): static
    {
        return app(static::class, ['livewire' => $livewire]);
    }

    public function getActions(): array
    {
        return $this->getLivewire()->getCachedTableActions();
    }

    public function getActionsPosition(): string
    {
        /** @var TableComponent $livewire */
        $livewire = $this->getLivewire();

        return invade($livewire)->getTableActionsPosition() ?? Position::AfterCells;
    }

    public function getActionsColumnLabel(): ?string
    {
        /** @var TableComponent $livewire */
        $livewire = $this->getLivewire();

        return invade($livewire)->getTableActionsColumnLabel();
    }

    public function getAllRecordsCount(): int
    {
        return $this->getLivewire()->getAllTableRecordsCount();
    }

    public function getBulkActions(): array
    {
        return array_filter(
            $this->getLivewire()->getCachedTableBulkActions(),
            fn (BulkAction $action): bool => ! $action->isHidden(),
        );
    }

    public function getColumns(): array
    {
        return array_filter(
            $this->getLivewire()->getCachedTableColumns(),
            fn (Column $column): bool => (! $column->isHidden()) && (! $column->isToggledHidden()),
        );
    }

    public function getContent(): ?View
    {
        /** @var TableComponent $livewire */
        $livewire = $this->getLivewire();

        return invade($livewire)->getTableContent();
    }

    public function getContentFooter(): ?View
    {
        /** @var TableComponent $livewire */
        $livewire = $this->getLivewire();

        return invade($livewire)->getTableContentFooter();
    }

    public function getDescription(): ?string
    {
        /** @var TableComponent $livewire */
        $livewire = $this->getLivewire();

        return invade($livewire)->getTableDescription();
    }

    public function getEmptyState(): ?View
    {
        /** @var TableComponent $livewire */
        $livewire = $this->getLivewire();

        return invade($livewire)->getTableEmptyState();
    }

    public function getEmptyStateActions(): array
    {
        return array_filter(
            $this->getLivewire()->getCachedTableEmptyStateActions(),
            fn (Action $action): bool => ! $action->isHidden(),
        );
    }

    public function getEmptyStateDescription(): ?string
    {
        /** @var TableComponent $livewire */
        $livewire = $this->getLivewire();

        return invade($livewire)->getTableEmptyStateDescription();
    }

    public function getEmptyStateHeading(): string
    {
        /** @var TableComponent $livewire */
        $livewire = $this->getLivewire();

        return invade($livewire)->getTableEmptyStateHeading() ?? __('tables::table.empty.heading');
    }

    public function getEmptyStateIcon(): string
    {
        /** @var TableComponent $livewire */
        $livewire = $this->getLivewire();

        return invade($livewire)->getTableEmptyStateIcon() ?? 'heroicon-o-x';
    }

    public function getFilters(): array
    {
        return $this->getLivewire()->getCachedTableFilters();
    }

    public function getFiltersForm(): ComponentContainer
    {
        return $this->getLivewire()->getTableFiltersForm();
    }

    public function getFiltersFormWidth(): ?string
    {
        /** @var TableComponent $livewire */
        $livewire = $this->getLivewire();

        return invade($livewire)->getTableFiltersFormWidth();
    }

    public function getFiltersLayout(): string
    {
        /** @var TableComponent $livewire */
        $livewire = $this->getLivewire();

        return invade($livewire)->getTableFiltersLayout() ?? Layout::Popover;
    }

    public function getColumnToggleForm(): ComponentContainer
    {
        return $this->getLivewire()->getTableColumnToggleForm();
    }

    public function getColumnToggleFormWidth(): ?string
    {
        /** @var TableComponent $livewire */
        $livewire = $this->getLivewire();

        return invade($livewire)->getTableColumnToggleFormWidth();
    }

    public function getHeader(): ?View
    {
        /** @var TableComponent $livewire */
        $livewire = $this->getLivewire();

        return invade($livewire)->getTableHeader();
    }

    public function getHeaderActions(): array
    {
        return array_filter(
            $this->getLivewire()->getCachedTableHeaderActions(),
            fn (Action | ActionGroup $action): bool => ! $action->isHidden(),
        );
    }

    public function getHeading(): ?string
    {
        /** @var TableComponent $livewire */
        $livewire = $this->getLivewire();

        return value(invade($livewire)->getTableHeading());
    }

    public function getModel(): string
    {
        /** @var TableComponent $livewire */
        $livewire = $this->getLivewire();

        return invade($livewire)->getTableQuery()->getModel()::class;
    }

    public function getMountedAction(): ?Action
    {
        return $this->getLivewire()->getMountedTableAction();
    }

    public function getMountedActionRecordKey()
    {
        return $this->getLivewire()->getMountedTableActionRecordKey();
    }

    public function getMountedActionForm(): ?ComponentContainer
    {
        return $this->getLivewire()->getMountedTableActionForm();
    }

    public function getMountedBulkAction(): ?BulkAction
    {
        return $this->getLivewire()->getMountedTableBulkAction();
    }

    public function getMountedBulkActionForm(): ?ComponentContainer
    {
        return $this->getLivewire()->getMountedTableBulkActionForm();
    }

    public function getRecords(): Collection | Paginator
    {
        return $this->getLivewire()->getTableRecords();
    }

    public function getRecordsPerPageSelectOptions(): array
    {
        /** @var TableComponent $livewire */
        $livewire = $this->getLivewire();

        return invade($livewire)->getTableRecordsPerPageSelectOptions();
    }

    public function getRecordAction(Model $record): ?string
    {
        /** @var TableComponent $livewire */
        $livewire = $this->getLivewire();

        $callback = invade($livewire)->getTableRecordActionUsing();

        if (! $callback) {
            return null;
        }

        return $callback($record);
    }

    public function getRecordClasses(Model $record): array
    {
        /** @var TableComponent $livewire */
        $livewire = $this->getLivewire();

        $callback = invade($livewire)->getTableRecordClassesUsing();

        if (! $callback) {
            return [];
        }

        return Arr::wrap($callback($record));
    }

    public function getRecordUrl(Model $record): ?string
    {
        /** @var TableComponent $livewire */
        $livewire = $this->getLivewire();

        $callback = invade($livewire)->getTableRecordUrlUsing();

        if (! $callback) {
            return null;
        }

        return $callback($record);
    }

    public function getReorderColumn(): ?string
    {
        /** @var TableComponent $livewire */
        $livewire = $this->getLivewire();

        return invade($livewire)->getTableReorderColumn();
    }

    public function isReorderable(): bool
    {
        /** @var TableComponent $livewire */
        $livewire = $this->getLivewire();

        return invade($livewire)->isTableReorderable();
    }

    public function isReordering(): bool
    {
        return $this->getLivewire()->isTableReordering();
    }

    public function getSortColumn(): ?string
    {
        return $this->getLivewire()->getTableSortColumn();
    }

    public function getSortDirection(): ?string
    {
        return $this->getLivewire()->getTableSortDirection();
    }

    public function isFilterable(): bool
    {
        return $this->getLivewire()->isTableFilterable();
    }

    public function isPaginationEnabled(): bool
    {
        /** @var TableComponent $livewire */
        $livewire = $this->getLivewire();

        return invade($livewire)->isTablePaginationEnabled();
    }

    public function isSelectionEnabled(): bool
    {
        return $this->getLivewire()->isTableSelectionEnabled();
    }

    public function isSearchable(): bool
    {
        return $this->getLivewire()->isTableSearchable();
    }

    public function isSearchableByColumn(): bool
    {
        return $this->getLivewire()->isTableSearchableByColumn();
    }

    public function hasToggleableColumns(): bool
    {
        return $this->getLivewire()->hasToggleableTableColumns();
    }

    public function getRecordKey(Model $record): string
    {
        return $this->getLivewire()->getTableRecordKey($record);
    }

    public function getPollingInterval(): ?string
    {
        /** @var TableComponent $livewire */
        $livewire = $this->getLivewire();

        return invade($livewire)->getTablePollingInterval();
    }

    public function isStriped(): bool
    {
        /** @var TableComponent $livewire */
        $livewire = $this->getLivewire();

        return invade($livewire)->isTableStriped();
    }
}
