<?php

namespace Filament\Actions\Concerns;

use Closure;

trait CanCancelParentAction
{
    protected bool | Closure $canCancelParentAction = false;

    public function cancelParent(bool | Closure $condition = true): static
    {
        $this->canCancelParentAction = $condition;

        return $this;
    }

    public function canCancelParentAction(): bool
    {
        return (bool) $this->evaluate($this->canCancelParentAction);
    }
}
