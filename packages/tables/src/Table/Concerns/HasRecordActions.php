<?php

namespace Filament\Tables\Table\Concerns;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Support\Enums\Size;
use Filament\Tables\Enums\RecordActionsPosition;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Arr;
use InvalidArgumentException;

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
        $this->removeCachedActions($this->getFlatRecordActions());

        $this->recordActions = [];
        $this->pushRecordActions($actions);

        if ($position) {
            $this->recordActionsPosition(is_string($position) ? (RecordActionsPosition::tryFrom($position) ?? $position) : $position);
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
        $defaultGroupDropdownPlacement = in_array($this->getRecordActionsPosition(), [RecordActionsPosition::BeforeCells, RecordActionsPosition::BeforeColumns])
            ? 'bottom-start'
            : 'bottom-end';

        return array_map(
            fn (Action | ActionGroup $action) => $action instanceof ActionGroup ? $action->defaultDropdownPlacement($defaultGroupDropdownPlacement)->defaultDropdownTeleport(true) : $action,
            $this->recordActions,
        );
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
     * @return array<string, Action>
     */
    public function getFlatRecordActions(): array
    {
        $flatActions = [];

        foreach ($this->recordActions as $action) {
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
