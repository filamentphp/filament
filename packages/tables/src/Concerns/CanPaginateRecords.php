<?php

namespace Filament\Tables\Concerns;

use Livewire\WithPagination;

trait CanPaginateRecords
{
    use WithPagination {
        WithPagination::resetPage as livewireResetPage;
    }

    public $tableRecordsPerPage;

    public function updatedTableRecordsPerPage(): void
    {
        if (config('tables.remember_per_page')) {
            session([$this->getPerPageRememberKey() => $this->getTableRecordsPerPage()]);
        }

        $this->resetPage();
    }

    protected function getTableRecordsPerPage(): int
    {
        return intval($this->tableRecordsPerPage);
    }

    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [5, 10, 25, 50];
    }

    protected function getDefaultTableRecordsPerPageSelectOption(): int
    {
        return config('tables.remember_per_page')
            ? session($this->getPerPageRememberKey(), 10)
            : 10;
    }

    protected function isTablePaginationEnabled(): bool
    {
        return true;
    }

    protected function getTablePaginationPageName(): string
    {
        return $this->getIdentifiedTableQueryStringPropertyNameFor('page');
    }

    public function getPerPageRememberKey(): string
    {
        return $this->getListingResourceName() . '_per_page';
    }

    public function getListingResourceName(): string
    {
        return (new \ReflectionClass($this))->getShortName();
    }

    public function resetPage(?string $pageName = null): void
    {
        $this->livewireResetPage($pageName ?? $this->getTablePaginationPageName());
    }
}
