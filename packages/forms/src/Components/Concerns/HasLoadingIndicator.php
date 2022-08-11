<?php

namespace Filament\Forms\Components\Concerns;

use Closure;

trait HasLoadingIndicator
{
    protected bool | Closure $hasLoadingIndicator = false;

    public function loadingIndicator(bool | Closure $condition = true): static
    {
        $this->hasLoadingIndicator = $condition;

        return $this;
    }

    public function hasLoadingIndicator(): bool
    {
        return (bool) $this->evaluate($this->hasLoadingIndicator);
    }
}
