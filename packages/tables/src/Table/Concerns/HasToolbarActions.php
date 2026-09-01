<?php

namespace Filament\Tables\Table\Concerns;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Illuminate\Support\Arr;
use InvalidArgumentException;

trait HasToolbarActions
{
    /**
     * @var array<Action | ActionGroup>
     */
    protected array $toolbarActions = [];

    /**
     * @param  array<Action | ActionGroup> | ActionGroup  $actions
     */
    public function toolbarActions(array | ActionGroup $actions): static
    {
        // We must remove the existing cached toolbar actions before setting the new ones, as
        // the visibility of the checkboxes is determined by which bulk actions are visible.
        // The `$this->flatActions` array is used to determine if any bulk actions are
        // visible. We cannot simply clear it, as the bulk actions defined in the toolbar
        // of the table are also stored in this array, and we do not want to remove them,
        // only the bulk actions that are stored in the `$this->toolbarActions` array.
        foreach ($this->toolbarActions as $existingAction) {
            if ($existingAction instanceof ActionGroup) {
                /** @var array<Action> $flatExistingActions */
                $flatExistingActions = $existingAction->getFlatActions();

                $this->removeCachedActions($flatExistingActions);
            } elseif ($existingAction instanceof Action) {
                $this->removeCachedActions([$existingAction]);
            }
        }

        $this->toolbarActions = [];

        $this->pushToolbarActions($actions);

        return $this;
    }

    /**
     * @param  array<Action | ActionGroup> | ActionGroup  $actions
     */
    public function pushToolbarActions(array | ActionGroup $actions): static
    {
        foreach (Arr::wrap($actions) as $action) {
            $action->table($this);

            if ($action instanceof ActionGroup) {
                /** @var array<string, Action> $flatActions */
                $flatActions = $action->getFlatActions();

                $this->mergeCachedFlatActions($flatActions);
            } elseif ($action instanceof Action) {
                $this->cacheAction($action);
            } else {
                throw new InvalidArgumentException('Table actions must be an instance of [' . Action::class . '] or [' . ActionGroup::class . '].');
            }

            $this->toolbarActions[] = $action;
        }

        return $this;
    }

    /**
     * @return array<Action | ActionGroup>
     */
    public function getToolbarActions(): array
    {
        return $this->toolbarActions;
    }
}
