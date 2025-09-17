<?php

namespace Filament\Schemas\Concerns;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;

trait CanModifyActions
{
    protected ?Closure $modifyActionsUsing = null;

    protected ?Closure $modifyActionGroupsUsing = null;

    public function modifyActionsUsing(?Closure $callback): static
    {
        $this->modifyActionsUsing = $callback;

        return $this;
    }

    public function modifyActionGroupsUsing(?Closure $callback): static
    {
        $this->modifyActionGroupsUsing = $callback;

        return $this;
    }

    public function modifyAction(Action $action): Action
    {
        if (! $this->modifyActionsUsing) {
            return $action;
        }

        return ($this->modifyActionsUsing)($action) ?? $action;
    }

    public function modifyActionGroup(ActionGroup $actionGroup): ActionGroup
    {
        if (! $this->modifyActionGroupsUsing) {
            return $actionGroup;
        }

        return ($this->modifyActionGroupsUsing)($actionGroup) ?? $actionGroup;
    }
}
