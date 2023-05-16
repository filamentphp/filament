<?php

namespace Filament\Actions\Concerns;

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
        return $this->evaluate($this->isDivided);
    }
}
