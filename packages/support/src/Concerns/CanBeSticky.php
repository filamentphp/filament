<?php

namespace Filament\Support\Concerns;

use Closure;

trait CanBeSticky
{
    protected bool | Closure $isHeaderSticky = false;

    public function stickyHeader(bool | Closure $condition = true): static
    {
        $this->isHeaderSticky = $condition;

        return $this;
    }

    public function isHeaderSticky(): bool
    {
        return $this->evaluate($this->isHeaderSticky);
    }
}
