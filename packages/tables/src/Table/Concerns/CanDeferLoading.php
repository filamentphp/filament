<?php

namespace Filament\Tables\Table\Concerns;

use Closure;

trait CanDeferLoading
{
    protected bool | Closure $isLoadingDeferred = false;

    public function deferLoading(bool | Closure $condition = true): static
    {
        $this->isLoadingDeferred = $condition;

        return $this;
    }

    public function isLoadingDeferred(): bool
    {
        return (bool) $this->evaluate($this->isLoadingDeferred);
    }

    public function isLoaded(): bool
    {
        return $this->getLivewire()->isTableLoaded();
    }
}
