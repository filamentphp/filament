<?php

namespace Filament\Tables;

use Filament\Forms\ComponentContainer;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Support\Traits\Tappable;
use Illuminate\View\Component as ViewComponent;

class Table extends ViewComponent implements Htmlable
{
    use Concerns\BelongsToLivewire;
    use Macroable;
    use Tappable;

    protected array $meta = [];

    final public function __construct(HasTable $livewire)
    {
        $this->livewire($livewire);
    }

    public static function make(HasTable $livewire): static
    {
        return new static($livewire);
    }

    public function getAllRecordsCount(): int
    {
        return $this->getLivewire()->getAllTableRecordsCount();
    }

    public function areAllRecordsOnCurrentPageSelected(): bool
    {
        return $this->getLivewire()->areAllTableRecordsOnCurrentPageSelected();
    }

    public function areAllRecordsSelected(): bool
    {
        return $this->getLivewire()->areAllTableRecordsSelected();
    }

    public function getAction(string $name): ?Action
    {
        return $this->getLivewire()->getCachedTableAction($name);
    }

    public function getActions(): array
    {
        return $this->getLivewire()->getCachedTableActions();
    }

    public function getBulkAction(string $name): ?BulkAction
    {
        return $this->getLivewire()->getCachedTableBulkAction($name);
    }

    public function getBulkActions(): array
    {
        return $this->getLivewire()->getCachedTableBulkActions();
    }

    public function getColumns(): array
    {
        return $this->getLivewire()->getCachedTableColumns();
    }

    public function getEmptyStateDescription(): ?string
    {
        return $this->getLivewire()->getTableEmptyStateDescription();
    }

    public function getEmptyStateHeading(): string
    {
        return $this->getLivewire()->getTableEmptyStateHeading();
    }

    public function getEmptyStateIcon(): string
    {
        return $this->getLivewire()->getTableEmptyStateIcon();
    }

    public function getEmptyStateView(): ?View
    {
        return $this->getLivewire()->getTableEmptyStateView();
    }

    public function getFilters(): array
    {
        return $this->getLivewire()->getCachedTableFilters();
    }

    public function getFiltersForm(): ComponentContainer
    {
        return $this->getLivewire()->getTableFiltersForm();
    }

    public function getMountedAction(): ?Action
    {
        return $this->getLivewire()->getMountedTableAction();
    }

    public function getMountedActionForm(): ComponentContainer
    {
        return $this->getLivewire()->getMountedTableActionForm();
    }

    public function getMountedBulkAction(): ?BulkAction
    {
        return $this->getLivewire()->getMountedTableBulkAction();
    }

    public function getMountedBulkActionForm(): ComponentContainer
    {
        return $this->getLivewire()->getMountedTableBulkActionForm();
    }

    public function getRecords(): Collection | LengthAwarePaginator
    {
        return $this->getLivewire()->getTableRecords();
    }

    public function getRecordsPerPageSelectOptions(): array
    {
        return $this->getLivewire()->getTableRecordsPerPageSelectOptions();
    }

    public function getSelectedRecordCount(): int
    {
        return $this->getLivewire()->getSelectedTableRecordCount();
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
        return $this->getLivewire()->isTablePaginationEnabled();
    }

    public function isRecordSelected(string $record): bool
    {
        return $this->getLivewire()->isTableRecordSelected($record);
    }

    public function isSelectionEnabled(): bool
    {
        return $this->getLivewire()->isTableSelectionEnabled();
    }

    public function isSearchable(): bool
    {
        return $this->getLivewire()->isTableSearchable();
    }

    public function toHtml(): string
    {
        return $this->render()->render();
    }

    public function render(): View
    {
        return view('tables::index', array_merge($this->data(), [
            'table' => $this,
        ]));
    }
}
