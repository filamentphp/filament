<?php

namespace Filament\Tables\Columns\Concerns;

trait CanCallAction
{
    public $action;

    public function action($action)
    {
        $this->action = $action;

        return $this;
    }
}
