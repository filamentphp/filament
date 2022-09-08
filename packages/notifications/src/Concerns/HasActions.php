<?php

namespace Filament\Notifications\Concerns;

use Closure;
use Filament\Notifications\Actions\ActionGroup;
use Illuminate\Support\Arr;

trait HasActions
{
    protected array | ActionGroup | Closure $actions = [];

    public function actions(array | ActionGroup | Closure $actions): static
    {
        $this->actions = $actions;

        return $this;
    }

    public function getActions(): array
    {
        return Arr::wrap($this->evaluate($this->actions));
    }
}
