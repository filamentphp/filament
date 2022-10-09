<?php

namespace Filament\Forms\Components\Concerns;

use Closure;

trait CanBePreloaded
{
    protected bool | Closure $isPreloaded = false;

    public function preload(bool | Closure $condition = true): static
    {
        $this->isPreloaded = $condition;

        return $this;
    }

    public function isPreloaded(): bool
    {
        return $this->evaluate($this->isPreloaded);
    }
}
