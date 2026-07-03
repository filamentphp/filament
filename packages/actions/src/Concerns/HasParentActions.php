<?php

namespace Filament\Actions\Concerns;

use Closure;

trait HasParentActions
{
    protected bool | string | Closure | null $cancelParentActions = null;

    protected bool | string | Closure | null $cancelParentActionsOnClose = null;

    protected bool | Closure $shouldOverlayParentActions = false;

    public function cancelParentActions(bool | string | Closure | null $toAction = true): static
    {
        $this->cancelParentActions = $toAction;

        return $this;
    }

    public function cancelParentActionsOnClose(bool | string | Closure | null $toAction = true): static
    {
        $this->cancelParentActionsOnClose = $toAction;

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

    public function shouldCancelParentActionsOnClose(): bool
    {
        $toAction = $this->evaluate($this->cancelParentActionsOnClose);

        return ($toAction === true) || is_string($toAction);
    }

    public function shouldCancelAllParentActionsOnClose(): bool
    {
        return $this->evaluate($this->cancelParentActionsOnClose) === true;
    }

    public function getParentActionToCancelToOnClose(): ?string
    {
        $toAction = $this->evaluate($this->cancelParentActionsOnClose);

        return is_string($toAction) ? $toAction : null;
    }

    public function shouldOverlayParentActions(): bool
    {
        return (bool) $this->evaluate($this->shouldOverlayParentActions);
    }
}
