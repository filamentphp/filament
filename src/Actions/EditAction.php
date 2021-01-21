<?php

namespace Filament\Actions;

use Filament\Action;

abstract class EditAction extends Action
{
    public $hasRouteParameter = true;

    public $record;

    public function mount($record)
    {
        $this->record = (new $this->resource)->getModel()::findOrFail($record);
    }
}
