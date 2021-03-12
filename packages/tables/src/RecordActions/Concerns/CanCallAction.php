<?php

namespace Filament\Tables\RecordActions\Concerns;

trait CanCallAction
{
    protected $action;

    public function action($action)
    {
        $this->configure(function () use ($action) {
            $this->action = $action;
        });

        return $this;
    }

    public function getAction()
    {
        return $this->action;
    }
}
