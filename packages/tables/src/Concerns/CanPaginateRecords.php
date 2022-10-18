<?php

namespace Filament\Tables\Concerns;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\WithPagination;

trait CanPaginateRecords
{
    use WithPagination {
        WithPagination::resetPage as livewireResetPage;
    }

    public $tableRecordsPerPage;

    protected int | string | null $defaultTableRecordsPerPageSelectOption = null;

    public function updatedTableRecordsPerPage(): void
    {
        session()->put([
            $this->getTablePerPageSessionKey() => $this->getTableRecordsPerPage(),
        ]);

        $this->resetPage();
    }

    protected function paginateTableQuery(Builder $query): Paginator
    {
        $perPage = $this->getTableRecordsPerPage();

        /** @var LengthAwarePaginator $records */
        $records = $query->paginate(
            $perPage === 'all' ? $query->count() : $perPage,
            ['*'],
            $this->getTablePaginationPageName(),
        );

        return $records->onEachSide(1);
    }

    protected function getTableRecordsPerPage(): int | string | null
    {
        return $this->tableRecordsPerPage;
    }

    /**
     * @deprecated Override the `table()` method to configure the table.
     */
    protected function getTableRecordsPerPageSelectOptions(): ?array
    {
        return null;
    }

    protected function getDefaultTableRecordsPerPageSelectOption(): int
    {
        $perPage = session()->get(
            $this->getTablePerPageSessionKey(),
            $this->defaultTableRecordsPerPageSelectOption ?? config('tables.pagination.default_records_per_page'),
        );

        $pageOptions = $this->getTable()->getPaginationPageOptions();

        if (in_array($perPage, $pageOptions)) {
            return $perPage;
        }

        session()->remove($this->getTablePerPageSessionKey());

        return $pageOptions[0];
    }

    /**
     * @deprecated Override the `table()` method to configure the table.
     */
    protected function isTablePaginationEnabled(): bool
    {
        return true;
    }

    protected function getTablePaginationPageName(): string
    {
        return $this->getIdentifiedTableQueryStringPropertyNameFor('page');
    }

    public function getTablePerPageSessionKey(): string
    {
        $table = class_basename($this::class);

        return "tables.{$table}_per_page";
    }

    public function resetPage(?string $pageName = null): void
    {
        $this->livewireResetPage($pageName ?? $this->getTablePaginationPageName());
    }
}
