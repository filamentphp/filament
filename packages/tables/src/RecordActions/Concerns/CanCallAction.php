<?php

namespace Filament\Tables\RecordActions\Concerns;

trait CanCallAction
{
    public $action;

    public function action($action)
    {
        $this->action = $action;

        return $this;
    }
}
