<?php

namespace Filament\Forms\Components\Concerns;

use Closure;

trait CanBeInline
{
    protected bool | Closure $isInline = true;

    public function inline(bool | Closure $condition = true): static
    {
        $this->isInline = $condition;

        return $this;
    }

    public function isInline(): bool
    {
        if ($this->hasInlineLabel()) {
            return false;
        }

        return (bool) $this->evaluate($this->isInline);
    }
}
