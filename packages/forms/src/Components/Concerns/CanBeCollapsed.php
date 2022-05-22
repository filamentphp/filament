<?php

namespace Filament\Forms\Components\Concerns;

use Closure;

trait CanBeCollapsed
{
    protected bool | Closure $isCollapsed = false;

    public function collapse(bool | Closure $condition = true): static
    {
        $this->isCollapsed = $condition;

        return $this;
    }

    public function isCollapsed(): string
    {
        return (bool) $this->evaluate($this->isCollapsed) ? 'true' : 'false';
    }
}
