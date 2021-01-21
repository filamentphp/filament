<?php

namespace Filament\Actions;

use Filament\Action;

abstract class IndexAction extends Action
{
    public $hasRouteParameter = false;

    public $records;

    public function mount()
    {
        $this->records = (new $this->resource)->getModel()::all();
    }
}
