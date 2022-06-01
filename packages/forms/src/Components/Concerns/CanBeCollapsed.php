<?php

namespace Filament\Forms\Components\Concerns;

use Closure;

trait CanBeCollapsed
{
    protected bool | Closure $isCollapsed = false;

    protected bool | Closure $isCollapsible = false;

    public function collapsed(bool | Closure $condition = true): static
    {
        $this->isCollapsed = $condition;

        return $this;
    }

    public function isCollapsed(): bool
    {
        return (bool) $this->evaluate($this->isCollapsed);
    }

    public function collapsible(bool | Closure $condition = true): static
    {
        $this->isCollapsible = $condition;

        return $this;
    }

    public function isCollapsible(): bool
    {
        return (bool) $this->evaluate($this->isCollapsible);
    }
}
