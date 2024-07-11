<?php

namespace Filament\Tables\Table\Concerns;

use Closure;
use Filament\Actions\Contracts\HasRecord;
use Filament\Support\Enums\ActionSize;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Enums\ActionsPosition;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use InvalidArgumentException;

trait HasActions
{
    /**
     * @var array<Action | ActionGroup>
     */
    protected array $actions = [];

    /**
     * @var array<string, Action>
     */
    protected array $flatActions = [];

    protected string | Htmlable | Closure | null $actionsColumnLabel = null;

    protected string | Closure | null $actionsAlignment = null;

    protected ActionsPosition | Closure | null $actionsPosition = null;

    /**
     * @param  array<Action | ActionGroup> | ActionGroup  $actions
     */
    public function actions(array | ActionGroup $actions, ActionsPosition | string | Closure | null $position = null): static
    {
        $this->actions = [];
        $this->pushActions($actions);

        if ($position) {
            $this->actionsPosition($position);
        }

        return $this;
    }

    /**
     * @param  array<Action | ActionGroup> | ActionGroup  $actions
     */
    public function pushActions(array | ActionGroup $actions): static
    {
        foreach (Arr::wrap($actions) as $action) {
            $action->table($this);

            if ($action instanceof ActionGroup) {
                /** @var array<string, Action> $flatActions */
                $flatActions = $action->getFlatActions();

                if (! $action->getDropdownPlacement()) {
                    $action->dropdownPlacement('bottom-end');
                }

                $this->mergeCachedFlatActions($flatActions);
            } elseif ($action instanceof Action) {
                $action->defaultSize(ActionSize::Small);
                $action->defaultView($action::LINK_VIEW);

                $this->cacheAction($action);
            } else {
                throw new InvalidArgumentException('Table actions must be an instance of ' . Action::class . ' or ' . ActionGroup::class . '.');
            }

            $this->actions[] = $action;
        }

        return $this;
    }

    public function actionsColumnLabel(string | Htmlable | Closure | null $label): static
    {
        $this->actionsColumnLabel = $label;

        return $this;
    }

    public function actionsAlignment(string | Closure | null $alignment = null): static
    {
        $this->actionsAlignment = $alignment;

        return $this;
    }

    public function actionsPosition(ActionsPosition | Closure | null $position = null): static
    {
        $this->actionsPosition = $position;

        return $this;
    }

    /**
     * @return array<Action | ActionGroup>
     */
    public function getActions(): array
    {
        return $this->actions;
    }

    /**
     * @param  string | array<string>  $name
     */
    public function getAction(string | array $name): ?Action
    {
        if (is_string($name) && str($name)->contains('.')) {
            $name = explode('.', $name);
        }

        if (is_array($name)) {
            $firstName = array_shift($name);
            $modalActionNames = $name;

            $name = $firstName;
        }

        $mountedRecord = $this->getLivewire()->getMountedTableActionRecord();

        $action = $this->getFlatActions()[$name] ?? null;

        if (! $action) {
            return null;
        }

        if (($actionGroup = $action->getRootGroup()) instanceof HasRecord) {
            $actionGroup->record($mountedRecord);
        }

        return $this->getMountableModalActionFromAction(
            $action->record($mountedRecord),
            modalActionNames: $modalActionNames ?? [],
            mountedRecord: $mountedRecord,
        );
    }

    /**
     * @return array<string, Action>
     */
    public function getFlatActions(): array
    {
        return $this->flatActions;
    }

    public function hasAction(string $name): bool
    {
        return array_key_exists($name, $this->getFlatActions());
    }

    protected function cacheAction(Action $action, bool $shouldOverwriteExistingAction = true): void
    {
        if ($shouldOverwriteExistingAction) {
            $this->flatActions[$action->getName()] = $action;
        } else {
            $this->flatActions[$action->getName()] ??= $action;
        }
    }

    /**
     * @param  array<string, Action>  $actions
     */
    protected function mergeCachedFlatActions(array $actions, bool $shouldOverwriteExistingActions = true): void
    {
        if ($shouldOverwriteExistingActions) {
            $this->flatActions = [
                ...$this->flatActions,
                ...$actions,
            ];
        } else {
            $this->flatActions = [
                ...$actions,
                ...$this->flatActions,
            ];
        }
    }

    /**
     * @param  array<string>  $modalActionNames
     */
    protected function getMountableModalActionFromAction(Action $action, array $modalActionNames, ?Model $mountedRecord = null): ?Action
    {
        $arguments = $this->getLivewire()->mountedTableActionsArguments ?? [];

        if (
            (($actionArguments = array_shift($arguments)) !== null) &&
            (! $action->hasArguments())
        ) {
            $action->arguments($actionArguments);
        }

        foreach ($modalActionNames as $modalActionName) {
            $action = $action->getMountableModalAction($modalActionName);

            if (! $action) {
                return null;
            }

            if ($action instanceof Action) {
                $action->record($mountedRecord);
            }

            if (
                (($actionArguments = array_shift($arguments)) !== null) &&
                (! $action->hasArguments())
            ) {
                $action->arguments($actionArguments);
            }
        }

        if (! $action instanceof Action) {
            return null;
        }

        return $action;
    }

    public function getActionsPosition(): ActionsPosition
    {
        $position = $this->evaluate($this->actionsPosition);

        if ($position) {
            return $position;
        }

        if (! ($this->getContentGrid() || $this->hasColumnsLayout())) {
            return ActionsPosition::AfterColumns;
        }

        return ActionsPosition::AfterContent;
    }

    public function getActionsAlignment(): ?string
    {
        return $this->evaluate($this->actionsAlignment);
    }

    public function getActionsColumnLabel(): string | Htmlable | null
    {
        return $this->evaluate($this->actionsColumnLabel);
    }
}
