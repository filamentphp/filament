<?php

namespace Filament\Tables\Table\Concerns;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Support\Enums\Size;
use Filament\Tables\Enums\RecordActionsPosition;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Arr;
use PHPUnit\Event\InvalidArgumentException;

trait HasRecordActions
{
    /**
     * @var array<Action | ActionGroup>
     */
    protected array $recordActions = [];

    protected string | Htmlable | Closure | null $recordActionsColumnLabel = null;

    protected string | Closure | null $recordActionsAlignment = null;

    protected RecordActionsPosition | Closure | null $recordActionsPosition = null;

    protected ?Closure $modifyUngroupedRecordActionsUsing = null;

    /**
     * @param  array<Action | ActionGroup> | ActionGroup  $actions
     */
    public function recordActions(array | ActionGroup $actions, RecordActionsPosition | string | Closure | null $position = null): static
    {
        $this->recordActions = [];
        $this->pushRecordActions($actions);

        if ($position) {
            $this->recordActionsPosition($position);
        }

        return $this;
    }

    /**
     * @param  array<Action | ActionGroup> | ActionGroup  $actions
     */
    public function pushRecordActions(array | ActionGroup $actions): static
    {
        foreach (Arr::wrap($actions) as $action) {
            $action->table($this);

            if ($action instanceof ActionGroup) {
                if (! $action->getDropdownPlacement()) {
                    $action->dropdownPlacement('bottom-end');
                }

                /** @var array<string, Action> $flatActions */
                $flatActions = $action->getFlatActions();

                $this->mergeCachedFlatActions($flatActions);
            } elseif ($action instanceof Action) {
                $action->defaultSize(Size::Small);
                $action->defaultView($action::LINK_VIEW);

                if ($this->modifyUngroupedRecordActionsUsing) {
                    $this->evaluate($this->modifyUngroupedRecordActionsUsing, ['action' => $action]);
                }

                $this->cacheAction($action);
            } else {
                throw new InvalidArgumentException('Table actions must be an instance of [' . Action::class . '] or [' . ActionGroup::class . '].');
            }

            $this->recordActions[] = $action;
        }

        return $this;
    }

    public function recordActionsColumnLabel(string | Htmlable | Closure | null $label): static
    {
        $this->recordActionsColumnLabel = $label;

        return $this;
    }

    public function recordActionsAlignment(string | Closure | null $alignment = null): static
    {
        $this->recordActionsAlignment = $alignment;

        return $this;
    }

    public function recordActionsPosition(RecordActionsPosition | Closure | null $position = null): static
    {
        $this->recordActionsPosition = $position;

        return $this;
    }

    /**
     * @return array<Action | ActionGroup>
     */
    public function getRecordActions(): array
    {
        return $this->recordActions;
    }

    public function getRecordActionsPosition(): RecordActionsPosition
    {
        $position = $this->evaluate($this->recordActionsPosition);

        if ($position) {
            return $position;
        }

        if (! ($this->getContentGrid() || $this->hasColumnsLayout())) {
            return RecordActionsPosition::AfterColumns;
        }

        return RecordActionsPosition::AfterContent;
    }

    public function getRecordActionsAlignment(): ?string
    {
        return $this->evaluate($this->recordActionsAlignment);
    }

    public function getRecordActionsColumnLabel(): string | Htmlable | null
    {
        return $this->evaluate($this->recordActionsColumnLabel);
    }

    /**
     * @deprecated Use `recordActions()` instead.
     *
     * @param  array<Action | ActionGroup> | ActionGroup  $actions
     */
    public function actions(array | ActionGroup $actions, RecordActionsPosition | string | Closure | null $position = null): static
    {
        $this->recordActions($actions, $position);

        return $this;
    }

    /**
     * @deprecated Use `pushRecordActions()` instead.
     *
     * @param  array<Action | ActionGroup> | ActionGroup  $actions
     */
    public function pushActions(array | ActionGroup $actions): static
    {
        $this->pushRecordActions($actions);

        return $this;
    }

    /**
     * @deprecated Use `recordActionsColumnLabel()` instead.
     */
    public function actionsColumnLabel(string | Htmlable | Closure | null $label): static
    {
        $this->recordActionsColumnLabel($label);

        return $this;
    }

    /**
     * @deprecated Use `recordActionsAlignment()` instead.
     */
    public function actionsAlignment(string | Closure | null $alignment = null): static
    {
        $this->recordActionsAlignment($alignment);

        return $this;
    }

    /**
     * @deprecated Use `recordActionsPosition()` instead.
     */
    public function actionsPosition(RecordActionsPosition | Closure | null $position = null): static
    {
        $this->recordActionsPosition($position);

        return $this;
    }

    /**
     * @deprecated Use `getRecordActions()` instead.
     *
     * @return array<Action | ActionGroup>
     */
    public function getActions(): array
    {
        return $this->getRecordActions();
    }

    /**
     * @deprecated Use `getRecordActionsPosition()` instead.
     */
    public function getActionsPosition(): RecordActionsPosition
    {
        return $this->getRecordActionsPosition();
    }

    /**
     * @deprecated Use `getRecordActionsAlignment()` instead.
     */
    public function getActionsAlignment(): ?string
    {
        return $this->getRecordActionsAlignment();
    }

    /**
     * @deprecated Use `getRecordActionsColumnLabel()` instead.
     */
    public function getActionsColumnLabel(): string | Htmlable | null
    {
        return $this->getRecordActionsColumnLabel();
    }

    /**
     * @return array<string, Action>
     */
    public function getFlatRecordActions(): array
    {
        $flatActions = [];

        foreach ($this->getRecordActions() as $action) {
            if ($action instanceof ActionGroup) {
                $flatActions = array_merge($flatActions, $action->getFlatActions());
            } else {
                $flatActions[$action->getName()] = $action;
            }
        }

        return $flatActions;
    }

    public function modifyUngroupedRecordActionsUsing(?Closure $callback = null): static
    {
        $this->modifyUngroupedRecordActionsUsing = $callback;

        return $this;
    }
}
