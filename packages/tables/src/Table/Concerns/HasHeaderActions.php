<?php

namespace Filament\Tables\Table\Concerns;

use Closure;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\Position;
use Illuminate\Support\Arr;
use InvalidArgumentException;

trait HasHeaderActions
{
    /**
     * @var array<string, Action | BulkAction | ActionGroup>
     */
    protected array $headerActions = [];

    protected string | Closure | null $headerActionsPosition = null;

    public function headerActionsPosition(string | Closure | null $position = null): static
    {
        $this->headerActionsPosition = $position;

        return $this;
    }

    /**
     * @param  array<Action | BulkAction | ActionGroup> | ActionGroup  $actions
     */
    public function headerActions(array | ActionGroup $actions, string | Closure | null $position = null): static
    {
        foreach (Arr::wrap($actions) as $index => $action) {
            if ($action instanceof ActionGroup) {
                foreach ($action->getActions() as $groupedAction) {
                    /** @phpstan-ignore-next-line */
                    if ((! $groupedAction instanceof Action) && (! $groupedAction instanceof BulkAction)) {
                        throw new InvalidArgumentException('Table header actions within a group must be an instance of ' . Action::class . ' or ' . BulkAction::class . '.');
                    }

                    $groupedAction->table($this);

                    if ($groupedAction instanceof BulkAction) {
                        $this->registerBulkAction($groupedAction);
                    }
                }

                $this->headerActions[$index] = $action;

                continue;
            }

            if ((! $action instanceof Action) && (! $action instanceof BulkAction)) {
                throw new InvalidArgumentException('Table header actions must be an instance of ' . Action::class . ', ' . BulkAction::class . ', or ' . ActionGroup::class . '.');
            }

            $action->defaultSize('sm');
            $action->table($this);

            if ($action instanceof BulkAction) {
                $this->registerBulkAction($action);
            }

            $this->headerActions[$action->getName()] = $action;
        }

        $this->headerActionsPosition($position);

        return $this;
    }

    public function getHeaderActionsPosition(): string
    {
        $position = $this->evaluate($this->headerActionsPosition);

        if (filled($position)) {
            return $position;
        }

        return Position::End;
    }

    /**
     * @return array<string, Action | BulkAction | ActionGroup>
     */
    public function getHeaderActions(): array
    {
        return $this->headerActions;
    }

    public function getHeaderAction(string $name): ?Action
    {
        $actions = $this->getHeaderActions();

        $action = $actions[$name] ?? null;

        if ($action) {
            return $action;
        }

        foreach ($actions as $action) {
            if (! $action instanceof ActionGroup) {
                continue;
            }

            $groupedAction = $action->getActions()[$name] ?? null;

            if (! $groupedAction) {
                continue;
            }

            return $groupedAction;
        }

        return null;
    }
}
