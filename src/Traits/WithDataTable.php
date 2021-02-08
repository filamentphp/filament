<?php

namespace Filament\Traits;

use Livewire\WithPagination;

trait WithDataTable
{
    use WithPagination;

    public $search;
    public $perPage = 10;
    public $sortField;
    public $sortDirection;

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortField = $field;
    }
}
