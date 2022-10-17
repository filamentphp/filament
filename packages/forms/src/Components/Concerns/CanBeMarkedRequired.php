<?php

namespace Filament\Forms\Components\Concerns;

use Closure;

trait CanBeMarkedRequired
{
    protected bool | Closure $shouldBeMarkedRequired = true;

    public function markRequired(bool | Closure $condition = true): static
    {
        $this->shouldBeMarkedRequired = $condition;

        return $this;
    }

    public function shouldBeMarkedRequired(): string
    {
        return $this->evaluate($this->shouldBeMarkedRequired);
    }
}
