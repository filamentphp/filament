<?php

namespace Filament\Infolists\Components\Concerns;

use Closure;

trait CanBeCollapsed
{
    protected bool | Closure $isCollapsed = false;

    protected bool | Closure | null $isCollapsible = null;

    public function collapsed(bool | Closure $condition = true, bool $shouldMakeComponentCollapsible = true): static
    {
        $this->isCollapsed = $condition;

        if ($shouldMakeComponentCollapsible && $this->isCollapsible === null) {
            $this->collapsible();
        }

        return $this;
    }

    public function isCollapsed(): bool
    {
        return (bool) $this->evaluate($this->isCollapsed);
    }

    public function collapsible(bool | Closure | null $condition = true): static
    {
        $this->isCollapsible = $condition;

        return $this;
    }

    public function isCollapsible(): bool
    {
        return (bool) $this->evaluate($this->isCollapsible);
    }
}
