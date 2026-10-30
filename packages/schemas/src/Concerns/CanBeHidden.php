<?php

namespace Filament\Schemas\Concerns;

use Closure;

trait CanBeHidden
{
    protected bool | Closure $isHidden = false;

    public function hidden(bool | Closure $condition = true): static
    {
        $this->isHidden = $condition;

        return $this;
    }

    public function isDirectlyHidden(): bool
    {
        return $this->evaluate($this->isHidden);
    }

    public function isHidden(): bool
    {
        return $this->isDirectlyHidden() || $this->getParentComponent()?->isHidden();
    }

    public function isVisible(): bool
    {
        return ! $this->isHidden();
    }

    public function isHiddenAndNotDehydratedWhenHidden(): bool
    {
        if (! $this->isHidden()) {
            return false;
        }

        return ! $this->getParentComponent()?->isDehydratedWhenHidden();
    }
}
