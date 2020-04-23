<?php

namespace Filament\Traits;

use Illuminate\Support\Facades\Schema;

trait FillsColumns
{
    /**
     * Make all columns fillable for the given model.
     * 
     * @return void
     */
    public function getFillable()
    {
        return Schema::getColumnListing($this->getTable());
    }
}
