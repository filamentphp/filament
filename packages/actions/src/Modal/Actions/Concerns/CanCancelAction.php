<?php

namespace Filament\Actions\Modal\Actions\Concerns;

use Closure;

trait CanCancelAction
{
    protected bool | Closure $canCancelAction = false;

    public function cancel(bool | Closure $condition = true): static
    {
        $this->canCancelAction = $condition;

        return $this;
    }

    public function canCancelAction(): bool
    {
        return (bool) $this->evaluate($this->canCancelAction);
    }
}
