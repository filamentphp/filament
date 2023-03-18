<?php

namespace Filament\Tables\Table\Concerns;

use Closure;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\Position;
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

    public function getAction(string $name): ?Action
    {
        $mountedRecord = $this->getLivewire()->getMountedTableActionRecord();

        $actions = $this->getActions();

        $action = $actions[$name] ?? null;

        if ($action) {
            return $action->record($mountedRecord);
        }

        foreach ($actions as $action) {
            if (! $action instanceof ActionGroup) {
                continue;
            }

            $groupedAction = $action->getActions()[$name] ?? null;

            if (! $groupedAction) {
                continue;
            }

            return $groupedAction->record($mountedRecord);
        }

        $actions = $this->columnActions;

        $action = $actions[$name] ?? null;

        if ($action) {
            return $action->record($mountedRecord);
        }

        return null;
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
