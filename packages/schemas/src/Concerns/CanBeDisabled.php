<?php

namespace Filament\Schemas\Concerns;

use Closure;

trait CanBeDisabled
{
    protected bool | Closure $isDisabled = false;

    public function disabled(bool | Closure $condition = true): static
    {
        $this->isDisabled = $condition;

        return $this;
    }

    public function isDirectlyDisabled(): bool
    {
        return (bool) $this->evaluate($this->isDisabled);
    }

    public function isDisabled(): bool
    {
        return $this->isDirectlyDisabled() || $this->getParentComponent()?->isDisabled();
    }

    public function isEnabled(): bool
    {
        return ! $this->isDisabled();
    }
}
