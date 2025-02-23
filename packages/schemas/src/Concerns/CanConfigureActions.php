<?php

namespace Filament\Schemas\Concerns;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;

trait CanConfigureActions
{
    protected ?Closure $configureActionsUsing = null;

    protected ?Closure $configureActionGroupsUsing = null;

    public function configureActionsUsing(?Closure $callback): static
    {
        $this->configureActionsUsing = $callback;

        return $this;
    }

    public function configureActionGroupsUsing(?Closure $callback): static
    {
        $this->configureActionGroupsUsing = $callback;

        return $this;
    }

    public function configureAction(Action $action): Action
    {
        if (! $this->configureActionsUsing) {
            return $action;
        }

        return ($this->configureActionsUsing)($action) ?? $action;
    }

    public function configureActionGroup(ActionGroup $actionGroup): ActionGroup
    {
        if (! $this->configureActionGroupsUsing) {
            return $actionGroup;
        }

        return ($this->configureActionGroupsUsing)($actionGroup) ?? $actionGroup;
    }
}
