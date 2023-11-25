<?php

namespace Filament\Widgets\Concerns;

use Exception;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Reactive;

use function Livewire\trigger;

trait InteractsWithPageTable
{
    /** @var array<string, int> */
    #[Reactive]
    public $paginators = [];

    /**
     * @var array<string, string | array<string, string | null> | null>
     */
    #[Reactive]
    public array $tableColumnSearches = [];

    #[Reactive]
    public ?string $tableGrouping = null;

    #[Reactive]
    public ?string $tableGroupingDirection = null;

    /**
     * @var array<string, mixed> | null
     */
    #[Reactive]
    public ?array $tableFilters = null;

    #[Reactive]
    public int | string | null $tableRecordsPerPage = null;

    /**
     * @var ?string
     */
    #[Reactive]
    public $tableSearch = '';

    #[Reactive]
    public ?string $tableSortColumn = null;

    #[Reactive]
    public ?string $tableSortDirection = null;

    #[Reactive]
    public ?string $activeTab = null;

    protected HasTable $tablePage;

    protected function getTablePage(): string
    {
        throw new Exception('You must define a `getTablePage()` method on your widget that returns the name of a Livewire component.');
    }

    protected function getTablePageInstance(): HasTable
    {
        if (isset($this->tablePage)) {
            return $this->tablePage;
        }

        /** @var HasTable $tableComponent */
        $page = app('livewire')->new($this->getTablePage());
        trigger('mount', $page, [], null, null);

        $page->activeTab = $this->activeTab;
        $page->paginators = $this->paginators;
        $page->tableColumnSearches = $this->tableColumnSearches;
        $page->tableFilters = $this->tableFilters;
        $page->tableGrouping = $this->tableGrouping;
        $page->tableGroupingDirection = $this->tableGroupingDirection;
        $page->tableRecordsPerPage = $this->tableRecordsPerPage;
        $page->tableSearch = $this->tableSearch;
        $page->tableSortColumn = $this->tableSortColumn;
        $page->tableSortDirection = $this->tableSortDirection;

        return $this->tablePage = $page;
    }

    protected function getPageTableQuery(): Builder
    {
        return $this->getTablePageInstance()->getFilteredSortedTableQuery();
    }

    protected function getPageTableRecords(): Collection | Paginator
    {
        return $this->getTablePageInstance()->getTableRecords();
    }
}
