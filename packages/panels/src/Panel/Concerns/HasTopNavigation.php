<?php

namespace Filament\Panel\Concerns;

use Closure;

trait HasTopNavigation
{
    protected bool | Closure $hasTopNavigation = false;

    public function topNavigation(bool | Closure $condition = true): static
    {
        $this->hasTopNavigation = $condition;

        return $this;
    }

    public function hasTopNavigation(): bool
    {
        return (bool) $this->evaluate($this->hasTopNavigation);
    }
}
