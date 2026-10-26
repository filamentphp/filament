<?php

namespace Filament\Tables\Table\Concerns;

use Closure;

trait CanDeferLoading
{
    /**
     * @deprecated Use `lazyLoaded()` and `isLazyLoaded()` instead.
     */
    protected bool | Closure $isLoadingDeferred = false;

    public function lazyLoaded(bool | Closure $condition = true): static
    {
        $this->isLoadingDeferred = $condition;

        return $this;
    }

    /**
     * @deprecated Use `lazyLoaded()` instead.
     */
    public function deferLoading(bool | Closure $condition = true): static
    {
        return $this->lazyLoaded($condition);
    }

    public function isLazyLoaded(): bool
    {
        return (bool) $this->evaluate($this->isLoadingDeferred);
    }

    /**
     * @deprecated Use `isLazyLoaded()` instead.
     */
    public function isLoadingDeferred(): bool
    {
        return $this->isLazyLoaded();
    }

    public function isLoaded(): bool
    {
        return $this->getLivewire()->isTableLoaded();
    }
}
