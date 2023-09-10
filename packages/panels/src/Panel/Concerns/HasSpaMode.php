<?php

namespace Filament\Panel\Concerns;

use Closure;

trait HasSpaMode
{
    protected bool | Closure $hasSpaMode = false;

    public function spa(bool | Closure $condition = true): static
    {
        $this->hasSpaMode = $condition;

        return $this;
    }

    public function hasSpaMode(): bool
    {
        return (bool) $this->evaluate($this->hasSpaMode);
    }
}
