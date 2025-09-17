<?php

namespace Filament\Schemas\Concerns;

use Closure;

trait CanBeHidden
{
    protected bool | Closure $isHidden = false;

    public function hidden(bool | Closure $hidden = true): static
    {
        $this->isHidden = $hidden;

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
}
