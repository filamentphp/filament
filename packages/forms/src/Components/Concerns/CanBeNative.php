<?php

namespace Filament\Forms\Components\Concerns;

use Closure;

trait CanBeNative
{
    protected bool | Closure $isNative = true;

    public function native(bool | Closure $condition = true): static
    {
        $this->isNative = $condition;

        return $this;
    }

    public function isNative(): bool
    {
        return (bool) $this->evaluate($this->isNative);
    }
}
