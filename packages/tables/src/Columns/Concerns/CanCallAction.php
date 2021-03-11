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

    public function getAction($record)
    {
        if ($this->action === null) return null;

        if (is_callable($this->action)) {
            $callback = $this->action;

            return $callback($record);
        }

        return $this->action;
    }
}
