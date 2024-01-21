<?php

namespace Filament\Forms\Components\Concerns;

use Closure;

trait CanBeMarkedAsRequired
{
    protected bool | Closure | null $isMarkedAsRequired = null;

    public function markAsRequired(bool | Closure | null $condition = true): static
    {
        $this->isMarkedAsRequired = $condition;

        return $this;
    }

    public function isMarkedAsRequired(): bool
    {
        return $this->evaluate($this->isMarkedAsRequired) ?? $this->isRequired();
    }
}
