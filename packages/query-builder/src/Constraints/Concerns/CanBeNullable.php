<?php

namespace Filament\QueryBuilder\Constraints\Concerns;

use Closure;

trait CanBeNullable
{
    protected bool | Closure $isNullable = false;

    public function nullable(bool | Closure $condition = true): static
    {
        $this->isNullable = $condition;

        return $this;
    }

    public function isNullable(): bool
    {
        return (bool) $this->evaluate($this->isNullable);
    }
}
