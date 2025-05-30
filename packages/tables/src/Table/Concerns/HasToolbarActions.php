<?php

namespace Filament\Tables\Table\Concerns;

use Closure;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\ToolbarActionsPosition;
use Illuminate\Support\Arr;
use InvalidArgumentException;

trait HasToolbarActions
{
    /**
     * @var array<string, Action | BulkAction | ActionGroup>
     */
    protected array $toolbarActions = [];

    protected ToolbarActionsPosition | Closure | null $toolbarActionsPosition = null;

    public function toolbarActionsPosition(ToolbarActionsPosition | Closure | null $position = null): static
    {
        $this->toolbarActionsPosition = $position;

        return $this;
    }

    /**
     * @param  array<Action | BulkAction | ActionGroup> | ActionGroup  $actions
     */
    public function toolbarActions(array | ActionGroup $actions, ToolbarActionsPosition | Closure | null $position = null): static
    {
        $this->toolbarActions = [];
        $this->pushToolbarActions($actions);

        if ($position) {
            $this->toolbarActionsPosition($position);
        }

        return $this;
    }

    /**
     * @param  array<Action | BulkAction | ActionGroup> | ActionGroup  $actions
     */
    public function pushToolbarActions(array | ActionGroup $actions): static
    {
        foreach (Arr::wrap($actions) as $action) {
            $action->table($this);

            if ($action instanceof ActionGroup) {
                foreach ($action->getFlatActions() as $flatAction) {
                    if ($flatAction instanceof BulkAction) {
                        $this->cacheBulkAction($flatAction);
                    } elseif ($flatAction instanceof Action) {
                        $this->cacheAction($flatAction);
                    }
                }
            } elseif ($action instanceof Action) {
                $this->cacheAction($action);
            } elseif ($action instanceof BulkAction) {
                $this->cacheBulkAction($action);
            } else {
                throw new InvalidArgumentException('Table header actions must be an instance of ' . Action::class . ', ' . BulkAction::class . ' or ' . ActionGroup::class . '.');
            }

            $this->toolbarActions[] = $action;
        }

        return $this;
    }

    public function getToolbarActionsPosition(): ToolbarActionsPosition
    {
        $position = $this->evaluate($this->toolbarActionsPosition);

        if (filled($position)) {
            return $position;
        }

        return ToolbarActionsPosition::End;
    }

    /**
     * @return array<string, Action | BulkAction | ActionGroup>
     */
    public function getToolbarActions(): array
    {
        return $this->toolbarActions;
    }
}
