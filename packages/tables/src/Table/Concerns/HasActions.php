<?php

namespace Filament\Tables\Table\Concerns;

use Closure;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\Position;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use InvalidArgumentException;

trait HasActions
{
    /**
     * @var array<string, Action | ActionGroup>
     */
    protected array $actions = [];

    protected string | Closure | null $actionsColumnLabel = null;

    protected string | Closure | null $actionsAlignment = null;

    protected string | Closure | null $actionsPosition = null;

    /**
     * @param  array<Action | ActionGroup> | ActionGroup  $actions
     */
    public function actions(array | ActionGroup $actions, string | Closure | null $position = null): static
    {
        foreach (Arr::wrap($actions) as $index => $action) {
            if ($action instanceof ActionGroup) {
                foreach ($action->getActions() as $groupedAction) {
                    if (! $groupedAction instanceof Action) {
                        throw new InvalidArgumentException('Table actions within a group must be an instance of ' . Action::class . '.');
                    }

                    $groupedAction->table($this);
                }

                $this->actions[$index] = $action;

                continue;
            }

            if (! $action instanceof Action) {
                throw new InvalidArgumentException('Table actions must be an instance of ' . Action::class . ' or ' . ActionGroup::class . '.');
            }

            $action->defaultSize('sm');
            $action->defaultView($action::LINK_VIEW);
            $action->table($this);

            $this->actions[$action->getName()] = $action;
        }

        $this->actionsPosition($position);

        return $this;
    }

    public function actionsColumnLabel(string | Closure | null $label): static
    {
        $this->actionsColumnLabel = $label;

        return $this;
    }

    public function actionsAlignment(string | Closure | null $alignment = null): static
    {
        $this->actionsAlignment = $alignment;

        return $this;
    }

    public function actionsPosition(string | Closure | null $position = null): static
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

        $actions = $this->getActions();

        $action = $actions[$name] ?? null;

        if ($action) {
            return $this->getMountableModalActionFromAction(
                $action->record($mountedRecord),
                modalActionNames: $modalActionNames ?? [],
                parentActionName: $name,
                mountedRecord: $mountedRecord,
            );
        }

        foreach ($actions as $action) {
            if (! $action instanceof ActionGroup) {
                continue;
            }

            $groupedAction = $action->getActions()[$name] ?? null;

            if (! $groupedAction) {
                continue;
            }

            return $this->getMountableModalActionFromAction(
                $groupedAction->record($mountedRecord),
                modalActionNames: $modalActionNames ?? [],
                parentActionName: $name,
                mountedRecord: $mountedRecord,
            );
        }

        $actions = $this->columnActions;

        $action = $actions[$name] ?? null;

        if (! $action) {
            return null;
        }

        return $this->getMountableModalActionFromAction(
            $action->record($mountedRecord),
            modalActionNames: $modalActionNames ?? [],
            parentActionName: $name,
            mountedRecord: $mountedRecord,
        );
    }

    /**
     * @param  array<string>  $modalActionNames
     */
    protected function getMountableModalActionFromAction(Action $action, array $modalActionNames, string $parentActionName, ?Model $mountedRecord = null): ?Action
    {
        foreach ($modalActionNames as $modalActionName) {
            $action = $action->getMountableModalAction($modalActionName);

            if (! $action) {
                throw new InvalidArgumentException("The [{$modalActionName}] action has not been registered on the [{$parentActionName}] table action.");
            }

            if ($action instanceof Action) {
                $action->record($mountedRecord);
            }

            $parentActionName = $modalActionName;
        }

        if (! $action instanceof Action) {
            return null;
        }

        return $action;
    }

    public function getActionsPosition(): string
    {
        $position = $this->evaluate($this->actionsPosition);

        if (filled($position)) {
            return $position;
        }

        if (! ($this->getContentGrid() || $this->hasColumnsLayout())) {
            return Position::AfterColumns;
        }

        $actions = $this->getActions();

        $firstAction = Arr::first($actions);

        if ($firstAction instanceof ActionGroup) {
            $firstAction->size('sm md:md');

            return Position::BottomCorner;
        }

        return Position::AfterContent;
    }

    public function getActionsAlignment(): ?string
    {
        return $this->evaluate($this->actionsAlignment);
    }

    public function getActionsColumnLabel(): ?string
    {
        return $this->evaluate($this->actionsColumnLabel);
    }
}
