<?php

namespace Filament\Tables\Table\Concerns;

use Closure;

trait CanBeStackedOnMobile
{
    protected bool | Closure $stackedOnMobile = false;

    public function stackedOnMobile(bool | Closure $condition = true): static
    {
        $this->stackedOnMobile = $condition;

        return $this;
    }

    public function isStackedOnMobile(): bool
    {
        return (bool) $this->evaluate($this->stackedOnMobile);
    }
}
