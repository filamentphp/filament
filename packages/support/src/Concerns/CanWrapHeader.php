<?php

namespace Filament\Support\Concerns;

use Closure;

trait CanWrapHeader
{
    protected bool | Closure $canHeaderWrap = false;

    public function wrapHeader(bool | Closure $condition = true): static
    {
        $this->canHeaderWrap = $condition;

        return $this;
    }

    public function canHeaderWrap(): bool
    {
        return (bool) $this->evaluate($this->canHeaderWrap);
    }
}
