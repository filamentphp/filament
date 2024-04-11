<?php

namespace Filament\Panel\Concerns;

use Closure;

trait HasBroadcasting
{
    protected bool | Closure $hasBroadcasting = true;

    public function broadcasting(bool | Closure $condition = true): static
    {
        $this->hasBroadcasting = $condition;

        return $this;
    }

    public function hasBroadcasting(): bool
    {
        return (bool) $this->evaluate($this->hasBroadcasting);
    }
}
