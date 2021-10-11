<?php

namespace Filament\Tables\Columns\Concerns;

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

    public function getAction($record)
    {
        $action = $this->action;

        if (
            $action === null &&
            $this->isPrimary() &&
            $this->getTable()->getPrimaryColumnAction()
        ) {
            $action = $this->getTable()->getPrimaryColumnAction();
        }

        if (is_callable($action)) {
            return $action($record);
        }

        return $action;
    }
}
