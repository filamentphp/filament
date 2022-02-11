<?php

namespace Filament\Tables\Filters\Concerns;

trait HasDefaultState
{
    protected $defaultState = null;

    public function default($state = true): static
    {
        $this->defaultState = $state;

        return $this;
    }

    public function getDefaultState()
    {
        return $this->evaluate($this->defaultState);
    }
}
