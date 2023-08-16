<?php

namespace Filament\Support\Concerns;

use Closure;

trait CanBeStickied
{
    protected bool | Closure $sticky = false;

    public function sticky(bool | Closure $condition = true): static
    {
        $this->sticky = $condition;

        return $this;
    }

    public function isSticky(): bool
    {
        return $this->evaluate($this->sticky);
    }
}
