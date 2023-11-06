<?php

namespace Filament\Tables\Table\Concerns;

use Closure;

trait HasLoadingOverlay
{
    protected bool | Closure $hasLoadingOverlay = false;

    public function loadingOverlay(bool | Closure $condition = true): static
    {
        $this->hasLoadingOverlay = $condition;

        return $this;
    }

    public function hasLoadingOverlay(): bool
    {
        return (bool) $this->evaluate($this->hasLoadingOverlay);
    }
}
