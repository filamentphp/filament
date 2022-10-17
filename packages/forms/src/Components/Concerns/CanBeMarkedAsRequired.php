<?php

namespace Filament\Forms\Components\Concerns;

use Closure;

trait CanBeMarkedAsRequired
{
    protected bool | Closure $isMarkedAsRequired = true;

    public function markAsRequired(bool | Closure $condition = true): static
    {
        $this->isMarkedAsRequired = $condition;

        return $this;
    }

    public function isMarkedAsRequired(): string
    {
        return $this->evaluate($this->isMarkedAsRequired);
    }
}
