<?php

namespace Filament\Forms\Components\Concerns;

use Closure;

trait CanBeMarkedAsRequired
{
    protected bool | Closure $shouldBeMarkedAsRequired = true;

    public function markAsRequired(bool | Closure $condition = true): static
    {
        $this->shouldBeMarkedAsRequired = $condition;

        return $this;
    }

    public function shouldBeMarkedAsRequired(): string
    {
        return $this->evaluate($this->shouldBeMarkedAsRequired);
    }
}
