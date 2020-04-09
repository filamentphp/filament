<?php

namespace Filament\Traits;

use Livewire\WithPagination;

trait WithDataTable 
{
    use WithPagination;
    
    public $perPage = 10;
    public $sortField;
    public $sortAsc = true;
    public $search = '';

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortAsc = ! $this->sortAsc;
        } else {
            $this->sortAsc = true;
        }

        $this->sortField = $field;
    }
}