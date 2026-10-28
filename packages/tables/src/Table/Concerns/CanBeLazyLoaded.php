<?php

namespace Filament\Tables\Table\Concerns;

use Closure;

trait CanBeLazyLoaded
{
    protected bool | Closure $isLazyLoaded = false;

    public function lazyLoaded(bool | Closure $condition = true): static
    {
        $this->isLazyLoaded = $condition;

        return $this;
    }

    public function isLazyLoaded(): bool
    {
        return (bool) $this->evaluate($this->isLazyLoaded);
    }

    public function isLoaded(): bool
    {
        return $this->getLivewire()->isTableLoaded();
    }
}
