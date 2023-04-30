<?php

namespace Filament\Actions\Concerns;

use Closure;

trait CanBeInline
{
    protected bool | Closure $isInline = false;

    public function inline(bool | Closure $condition = true): static
    {
        $this->isInline = $condition;

        return $this;
    }

    public function isInline(): bool
    {
        return $this->evaluate($this->isInline);
    }
}
