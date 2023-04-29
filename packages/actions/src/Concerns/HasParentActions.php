<?php

namespace Filament\Actions\Concerns;

use Closure;

trait HasParentActions
{
    protected bool | string | Closure | null $closeParentActions = null;

    public function closeParentActions(bool | string | Closure | null $toAction = true): static
    {
        $this->closeParentActions = $toAction;

        return $this;
    }

    public function shouldCloseAllParentActions(): bool
    {
        return $this->evaluate($this->closeParentActions) === true;
    }

    public function getParentActionToCloseTo(): ?string
    {
        return $this->evaluate($this->closeParentActions);
    }
}
