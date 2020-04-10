<?php

namespace Filament\Traits;

use Livewire\WithPagination;

trait WithDataTable 
{
    use WithPagination;
    
    public $perPage = 12;
    public $pagingOptions = [12, 24, 36];
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