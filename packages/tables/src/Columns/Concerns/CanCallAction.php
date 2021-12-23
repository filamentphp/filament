<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;

trait CanCallAction
{
    protected string | Closure | null $action = null;

    public function action(string | Closure | null $action): static
    {
        $this->action = $action;

        return $this;
    }

    public function callAction()
    {
        $action = $this->getAction();

        if (! $action instanceof Closure) {
            return;
        }

        return app()->call($action, [
            'livewire' => $this->getLivewire(),
            'record' => $this->getRecord(),
        ]);
    }

    public function getAction(): string | Closure | null
    {
        return $this->action;
    }
}
