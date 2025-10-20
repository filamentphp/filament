<?php

namespace Filament\Schemas\Components\Concerns;

use Closure;

trait CanBeDivided
{
    protected bool | Closure $isDivided = false;

    public function divided(bool | Closure $condition = true): static
    {
        $this->isDivided = $condition;

        return $this;
    }

    public function isDivided(): bool
    {
        return (bool) $this->evaluate($this->isDivided);
    }
}
