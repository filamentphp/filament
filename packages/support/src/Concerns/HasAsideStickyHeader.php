<?php

namespace Filament\Support\Concerns;

use Closure;

trait HasAsideStickyHeader
{
    protected bool | Closure $isAsideHeaderSticky = false;

    public function asideStickyHeader(bool | Closure $condition = true): static
    {
        $this->isAsideHeaderSticky = $condition;

        return $this;
    }

    public function isAsideHeaderSticky(): bool
    {
        return (bool) $this->evaluate($this->isAsideHeaderSticky);
    }
}
