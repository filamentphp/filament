<?php

namespace Filament\Actions;

use Filament\Action;

abstract class IndexAction extends Action
{
    public $hasRouteParameter = false;

    public function mount()
    {
        $this->records = (new $this->resource)->getModel()::all();

        dd($this->getTitle(), $this->records);
    }
}
