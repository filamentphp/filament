<?php

namespace Filament\Tables\Filters\Concerns;

use Closure;

trait CanBeHidden
{
    protected bool | Closure $isHidden = false;

    public function hidden(bool | Closure $condition = true): static
    {
        $this->isHidden = $condition;

        return $this;
    }

    public function isHidden(): bool
    {
        return $this->evaluate($this->isHidden);
    }
}
