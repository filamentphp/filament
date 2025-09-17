<?php

namespace Filament\Tables\Table\Concerns;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Tables\Actions\HeaderActionsPosition;
use Illuminate\Support\Arr;
use InvalidArgumentException;

trait HasHeaderActions
{
    /**
     * @var array<string, Action | BulkAction | ActionGroup>
     */
    protected array $headerActions = [];

    protected HeaderActionsPosition | Closure | null $headerActionsPosition = null;

    public function headerActionsPosition(HeaderActionsPosition | Closure | null $position = null): static
    {
        $this->headerActionsPosition = $position;

        return $this;
    }

    /**
     * @param  array<Action | BulkAction | ActionGroup> | ActionGroup  $actions
     */
    public function headerActions(array | ActionGroup $actions, HeaderActionsPosition | Closure | null $position = null): static
    {
        // We must remove the existing cached header actions before setting the new ones, as
        // the visibility of the checkboxes is determined by which bulk actions are visible.
        // The `$this->flatActions` array is used to determine if any bulk actions are
        // visible. We cannot simply clear it, as the bulk actions defined in the header
        // of the table are also stored in this array, and we do not want to remove them,
        // only the bulk actions that are stored in the `$this->headerActions` array.
        foreach ($this->headerActions as $existingAction) {
            if ($existingAction instanceof ActionGroup) {
                /** @var array<Action> $flatExistingActions */
                $flatExistingActions = $existingAction->getFlatActions();

                $this->removeCachedActions($flatExistingActions);
            } elseif ($existingAction instanceof Action) {
                $this->removeCachedActions([$existingAction]);
            }
        }

        $this->headerActions = [];

        $this->pushHeaderActions($actions);

        if ($position) {
            $this->headerActionsPosition($position);
        }

        return $this;
    }

    /**
     * @param  array<Action | BulkAction | ActionGroup> | ActionGroup  $actions
     */
    public function pushHeaderActions(array | ActionGroup $actions): static
    {
        foreach (Arr::wrap($actions) as $action) {
            $action->table($this);

            if ($action instanceof ActionGroup) {
                foreach ($action->getFlatActions() as $flatAction) {
                    $this->cacheAction($flatAction);
                }
            } elseif ($action instanceof Action) {
                $this->cacheAction($action);
            } else {
                throw new InvalidArgumentException('Table header actions must be an instance of [' . Action::class . '], [' . BulkAction::class . '] or [' . ActionGroup::class . '].');
            }

            $this->headerActions[] = $action;
        }

        return $this;
    }

    public function getHeaderActionsPosition(): HeaderActionsPosition
    {
        $position = $this->evaluate($this->headerActionsPosition);

        if (filled($position)) {
            return $position;
        }

        return HeaderActionsPosition::Adaptive;
    }

    /**
     * @return array<string, Action | BulkAction | ActionGroup>
     */
    public function getHeaderActions(): array
    {
        return $this->headerActions;
    }
}
