<?php

namespace Filament\Notifications\Concerns;

use Closure;

trait HasActions
{
    protected array | Closure $actions = [];

    public function actions(array | Closure $actions): static
    {
        $this->actions = $actions;

        return $this;
    }

    public function getActions(): array
    {
        return $this->evaluate($this->actions);
    }
}
