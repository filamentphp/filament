<?php

namespace Filament\Tables\Concerns;

use Livewire\WithPagination;

trait CanPaginateRecords
{
    use WithPagination;

    public $hasPagination = true;

    public $recordsPerPage = 25;

    public function hasPagination()
    {
        return $this->hasPagination;
    }

    public function setPage($page)
    {
        $this->page = $page;

        $this->selected = [];
    }

    public function updatedRecordsPerPage()
    {
        $this->selected = [];

        if (! $this->hasPagination()) {
            return;
        }

        $this->resetPage();
    }
}
