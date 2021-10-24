<?php

namespace Filament\Tables;

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

    public function getRecords(): Collection | LengthAwarePaginator
    {
        return $this->getLivewire()->getTableRecords();
    }

    public function isFilterable(): bool
    {
        return $this->getLivewire()->isTableFilterable();
    }

    public function isPaginationEnabled(): bool
    {
        return $this->getLivewire()->isTablePaginationEnabled();
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
