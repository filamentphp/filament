<?php

namespace Filament\Tables\Filters\Concerns;

use Closure;

trait HasDefaultState
{
    protected bool | Closure | null $defaultState = null;

    public function default(bool | Closure | null $state = true): static
    {
        $this->defaultState = $state;

        return $this;
    }

    public function getDefaultState()
    {
        return $this->evaluate($this->defaultState);
    }
}
