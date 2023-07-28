<?php

namespace Filament\Actions\Concerns;

use Closure;

trait HasParentActions
{
    protected bool | string | Closure | null $cancelParentActions = null;

    public function cancelParentActions(bool | string | Closure | null $toAction = true): static
    {
        $this->cancelParentActions = $toAction;

        return $this;
    }

    public function shouldCancelAllParentActions(): bool
    {
        return $this->evaluate($this->cancelParentActions) === true;
    }

    public function getParentActionToCancelTo(): ?string
    {
        return $this->evaluate($this->cancelParentActions);
    }
}
