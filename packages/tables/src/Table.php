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

    protected ?string $emptyStateDescription = null;

    protected ?string $emptyStateHeading = null;

    protected ?string $emptyStateIcon = null;

    protected ?View $emptyStateView = null;

    protected bool $isPaginationEnabled = true;

    protected array $meta = [];

    protected ?array $recordsPerPageSelectOptions = null;

    final public function __construct(HasTable $livewire)
    {
        $this->livewire($livewire);
    }

    public static function make(HasTable $livewire): static
    {
        return new static($livewire);
    }

    public function emptyStateDescription(?string $description): static
    {
        $this->emptyStateDescription = $description;

        return $this;
    }

    public function emptyStateHeading(?string $heading): static
    {
        $this->emptyStateHeading = $heading;

        return $this;
    }

    public function emptyStateIcon(?string $icon): static
    {
        $this->emptyStateIcon = $icon;

        return $this;
    }

    public function emptyStateView(?View $view): static
    {
        $this->emptyStateView = $view;

        return $this;
    }

    public function enablePagination(bool $condition = true): static
    {
        $this->isPaginationEnabled = $condition;

        return $this;
    }

    public function recordsPerPageSelectOptions(array $options): static
    {
        $this->recordsPerPageSelectOptions = $options;

        return $this;
    }

    public function areAllRecordsOnCurrentPageSelected(): bool
    {
        return $this->getLivewire()->areAllTableRecordsOnCurrentPageSelected();
    }

    public function areAllRecordsSelected(): bool
    {
        return $this->getLivewire()->areAllTableRecordsSelected();
    }

    public function getActions(): array
    {
        return $this->getLivewire()->getCachedTableActions();
    }

    public function getAllRecordsCount(): int
    {
        return $this->getLivewire()->getAllTableRecordsCount();
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
        return $this->emptyStateDescription;
    }

    public function getEmptyStateHeading(): string
    {
        return $this->emptyStateHeading ?? __('tables::table.empty.heading');
    }

    public function getEmptyStateIcon(): string
    {
        return $this->emptyStateIcon ?? 'heroicon-o-x';
    }

    public function getEmptyStateView(): ?View
    {
        return $this->emptyStateView;
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
        return $this->recordsPerPageSelectOptions;
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
        return $this->isPaginationEnabled;
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
