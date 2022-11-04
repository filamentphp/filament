<?php

namespace Filament\Tables\Filters\Concerns;

use Closure;

trait HasDefaultState
{
    protected Closure | null $defaultState = null;

    public function default(Closure | null $state ): static
    {
        $this->defaultState = $state;

        return $this;
    }

    public function getDefaultState()
    {
        return $this->evaluate($this->defaultState);
    }
}
