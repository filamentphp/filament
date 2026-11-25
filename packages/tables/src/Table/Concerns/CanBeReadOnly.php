<?php

namespace Filament\Tables\Table\Concerns;

use Closure;

trait CanBeReadOnly
{
    protected bool | Closure $isReadOnly = false;

    public function readOnly(bool | Closure $condition = true): static
    {
        $this->isReadOnly = $condition;

        return $this;
    }

    public function isReadOnly(): bool
    {
        return (bool) $this->evaluate($this->isReadOnly);
    }
}
