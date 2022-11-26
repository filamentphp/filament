<?php

namespace Filament\Tables\Filters\Concerns;

trait HasDefaultState
{
    /**
     * @var mixed
     */
    protected $defaultState = null;

    /**
     * @param  mixed  $state
     */
    public function default($state = true): static
    {
        $this->defaultState = $state;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDefaultState()
    {
        return $this->evaluate($this->defaultState);
    }
}
