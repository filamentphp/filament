<?php

namespace Filament\Tables\Concerns;

use Livewire\WithPagination;

trait CanPaginateRecords
{
    use WithPagination;

    public bool $hasPagination = true;

    public int $recordsPerPage = 25;

    public function hasPagination(): bool
    {
        return $this->hasPagination;
    }

    public function setPage($page, $pageName = 'page'): void
    {
        $this->syncInput('paginators.' . $pageName, $page);
        $this->syncInput($pageName, $page);

        $this->selected = [];
    }

    public function updatedRecordsPerPage(): void
    {
        $this->selected = [];

        if (! $this->hasPagination()) {
            return;
        }

        $this->resetPage();
    }
}
