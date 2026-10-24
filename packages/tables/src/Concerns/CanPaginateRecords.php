<?php

namespace Filament\Tables\Concerns;

use Filament\Support\Components\Attributes\Unexposed;
use Filament\Tables\Enums\PaginationMode;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Locked;

trait CanPaginateRecords
{
    /**
     * @var int | string | null
     */
    public $tableRecordsPerPage = null;

    #[Locked]
    public int | string | null $initialTableRecordsPerPage = null;

    protected int | string | null $defaultTableRecordsPerPageSelectOption = null;

    #[Unexposed]
    public function updatedTableRecordsPerPage(): void
    {
        if ($this->getTable()->enforcesPaginationPageOptions()) {
            $this->tableRecordsPerPage = $this->getTableRecordsPerPage();
        }

        if ($this->getTable()->persistsRecordsPerPageInSession()) {
            session()->put([
                $this->getTablePerPageSessionKey() => $this->getTableRecordsPerPage(),
            ]);
        }

        $this->resetPage();
    }

    protected function paginateTableQuery(Builder $query): Paginator | CursorPaginator
    {
        $perPage = $this->getTableRecordsPerPage();

        $mode = $this->getTable()->getPaginationMode();

        if ($mode === PaginationMode::Simple) {
            return $query->simplePaginate(
                perPage: ($perPage === 'all') ? $query->toBase()->getCountForPagination() : $perPage,
                pageName: $this->getTablePaginationPageName(),
            );
        }

        if ($mode === PaginationMode::Cursor) {
            return $query->cursorPaginate(
                perPage: ($perPage === 'all') ? $query->toBase()->getCountForPagination() : $perPage,
                cursorName: $this->getTablePaginationPageName(),
            );
        }

        $total = $query->toBase()->getCountForPagination();

        /** @var LengthAwarePaginator $records */
        $records = $query->paginate(
            perPage: ($perPage === 'all') ? $total : $perPage,
            pageName: $this->getTablePaginationPageName(),
            total: $total,
        );

        return $records->onEachSide(0);
    }

    #[Unexposed]
    public function getTableRecordsPerPage(): int | string | null
    {
        if (
            $this->getTable()->enforcesPaginationPageOptions() &&
            (
                ((! is_int($this->tableRecordsPerPage)) && (! is_string($this->tableRecordsPerPage))) ||
                (! in_array($this->tableRecordsPerPage, $this->getTable()->getPaginationPageOptions()))
            )
        ) {
            return $this->getDefaultTableRecordsPerPageSelectOption();
        }

        return $this->tableRecordsPerPage;
    }

    #[Unexposed]
    public function getTablePage(): int | string
    {
        return $this->getPage($this->getTablePaginationPageName());
    }

    #[Unexposed]
    public function getDefaultTableRecordsPerPageSelectOption(): int | string
    {
        $defaultOption = $this->defaultTableRecordsPerPageSelectOption ?? $this->getTable()->getDefaultPaginationPageOption();
        $option = $this->getTable()->persistsRecordsPerPageInSession()
            ? session()->get($this->getTablePerPageSessionKey(), $defaultOption)
            : $defaultOption;

        $pageOptions = $this->getTable()->getPaginationPageOptions();

        if ($this->getTable()->enforcesPaginationPageOptions() && (! count($pageOptions))) {
            return $defaultOption ?? $this->initialTableRecordsPerPage ?? 10;
        }

        if (
            ((! $this->getTable()->enforcesPaginationPageOptions()) || is_int($option) || is_string($option)) &&
            in_array($option, $pageOptions)
        ) {
            return $option;
        }

        if ($this->getTable()->persistsRecordsPerPageInSession()) {
            session()->remove($this->getTablePerPageSessionKey());
        }

        return $pageOptions[0];
    }

    #[Unexposed]
    public function getTablePaginationPageName(): string
    {
        return $this->getIdentifiedTableQueryStringPropertyNameFor('page');
    }

    #[Unexposed]
    public function getTablePerPageSessionKey(): string
    {
        return $this->getTableSessionKey('per_page');
    }

    /**
     * @deprecated Override the `table()` method to configure the table.
     *
     * @return array<int | string> | null
     */
    protected function getTableRecordsPerPageSelectOptions(): ?array
    {
        return null;
    }

    /**
     * @deprecated Override the `table()` method to configure the table.
     */
    protected function isTablePaginationEnabled(): bool
    {
        return true;
    }
}
