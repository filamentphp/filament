<?php

namespace Filament\Tables\Filters\Concerns;

trait HasDefaultState
{
    protected mixed $defaultState = null;

    public function default(mixed $state = true): static
    {
        $this->defaultState = $state;

        return $this;
    }

    public function getDefaultState(): mixed
    {
        return $this->evaluate($this->defaultState);
    }
}
