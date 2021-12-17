<?php

namespace Filament\Tables\Concerns;

use Livewire\WithPagination;
use Illuminate\Support\Facades\Cache;

trait CanPaginateRecords
{
    use WithPagination {
        WithPagination::resetPage as livewireResetPage;
    }

    public $tableRecordsPerPage;

    public function updatedTableRecordsPerPage(): void
    {
        if (config('tables.remember_per_page')) {
            Cache::forever($this->getPerPageRememberKey(), $this->getTableRecordsPerPage());
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
            ? Cache::get($this->getPerPageRememberKey(), 10)
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
        return auth()->user()->id
            . '_'
            . $this->getTablePaginationPageName()
            . '_'
            . 'per_page';
    }

    public function resetPage(?string $pageName = null): void
    {
        $this->livewireResetPage($pageName ?? $this->getTablePaginationPageName());
    }
}
