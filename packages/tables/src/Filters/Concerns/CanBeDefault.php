<?php

namespace Filament\Tables\Filters\Concerns;

use Closure;

trait CanBeDefault
{
    protected $defaultState = null;

    public function default($state = true): static
    {
        $this->defaultState = $state;

        return $this;
    }

    public function getDefaultState(): bool
    {
        return $this->evaluate($this->defaultState);
    }
}
