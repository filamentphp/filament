<?php

namespace Filament\Widgets\Concerns;

use Exception;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Reactive;

use function Livewire\trigger;

trait InteractsWithPageTable /** @phpstan-ignore trait.unused */
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
    public ?string $tableSort = null;

    #[Reactive]
    public ?string $activeTab = null;

    #[Reactive] #[Locked]
    public ?Model $parentRecord = null;

    protected HasTable $tablePage;

    protected function getTablePage(): string
    {
        throw new Exception('You must define a `getTablePage()` method on your widget that returns the name of a Livewire component.');
    }

    /**
     * @return array<string, mixed>
     */
    protected function getTablePageMountParameters(): array
    {
        return [];
    }

    protected function getTablePageInstance(): HasTable
    {
        if (isset($this->tablePage)) {
            return $this->tablePage;
        }

        /** @var HasTable $tableComponent */
        $page = app('livewire')->new($this->getTablePage());
        trigger('mount', $page, [
            'activeTab' => $this->activeTab,
            'paginators' => $this->paginators,
            'parentRecord' => $this->parentRecord,
            'tableColumnSearches' => $this->tableColumnSearches,
            'tableFilters' => $this->tableFilters,
            'tableGrouping' => $this->tableGrouping,
            'tableRecordsPerPage' => $this->tableRecordsPerPage,
            'tableSearch' => $this->tableSearch,
            'tableSort' => $this->tableSort,
            ...$this->getTablePageMountParameters(),
        ], null, null);

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
