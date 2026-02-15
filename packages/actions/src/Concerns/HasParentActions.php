<?php

namespace Filament\Actions\Concerns;

use Closure;

trait HasParentActions
{
    protected bool | string | Closure | null $cancelParentActions = null;

    protected bool | Closure $shouldOverlayParentActions = false;

    public function cancelParentActions(bool | string | Closure | null $toAction = true): static
    {
        $this->cancelParentActions = $toAction;

        return $this;
    }

    public function overlayParentActions(bool | Closure $condition = true): static
    {
        $this->shouldOverlayParentActions = $condition;

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

    public function shouldOverlayParentActions(): bool
    {
        return (bool) $this->evaluate($this->shouldOverlayParentActions);
    }
}
