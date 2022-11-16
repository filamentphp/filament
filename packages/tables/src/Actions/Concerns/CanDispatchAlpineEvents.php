<?php

namespace Filament\Tables\Actions\Concerns;

use Closure;

trait CanDispatchAlpineEvents
{
    protected null | array | Closure $dispatchData = null;

    public function dispatchData(array | Closure $dispatchData): static
    {
        $this->dispatchData = $dispatchData;

        return $this;
    }

    public function getDispatchData(): string
    {
        return $this->evaluate($this->dispatchData);
    }
}
