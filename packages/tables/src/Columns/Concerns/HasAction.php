<?php

namespace Filament\Tables\Columns\Concerns;

trait HasAction
{
    public $action;

    public function action($action)
    {
        $this->action = $action;

        return $this;
    }
}
