<?php

namespace Filament\Tables\Table\Concerns;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Size;
use Filament\Tables\Enums\RecordActionsPosition;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\View\Factory;
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
     * @var array{actionsByRecordKey: array<string, array<Action | ActionGroup>>, hasVisibleActions: bool} | null
     */
    protected ?array $cachedRenderRecordActionScan = null;

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
        if ($this->isReadOnly()) {
            return [];
        }

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

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     *
     * @return array{actionsByRecordKey: array<string, array<Action | ActionGroup>>, hasVisibleActions: bool}
     */
    protected function getRenderRecordActionScan(): array
    {
        if ($this->cachedRenderRecordActionScan !== null) {
            return $this->cachedRenderRecordActionScan;
        }

        $actionsByRecordKey = [];

        if ($this->isLoaded() && (! $this->isReordering())) {
            // Retain the hidden prefix and first visible clones for the row loop.
            foreach ($this->getRecords() as $record) {
                $actionsByRecordKey[$this->getRecordKey($record)] = $actions = $this->getVisibleRenderRecordActions($record);

                if ($actions !== []) {
                    return $this->cachedRenderRecordActionScan = ['actionsByRecordKey' => $actionsByRecordKey, 'hasVisibleActions' => true];
                }
            }
        }

        return $this->cachedRenderRecordActionScan = ['actionsByRecordKey' => $actionsByRecordKey, 'hasVisibleActions' => false];
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function hasRenderRecordActions(): bool
    {
        return $this->getRenderRecordActionScan()['hasVisibleActions'];
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     *
     * @param  Model | array<string, mixed>  $record
     * @return array<Action | ActionGroup>
     */
    protected function getRenderRecordActions(Model | array $record, string $recordKey): array
    {
        $scan = $this->getRenderRecordActionScan();

        return $scan['actionsByRecordKey'][$recordKey]
            ?? ($scan['hasVisibleActions'] ? $this->getVisibleRenderRecordActions($record) : []);
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     *
     * @param  Model | array<string, mixed>  $record
     * @return array<Action | ActionGroup>
     */
    protected function getVisibleRenderRecordActions(Model | array $record): array
    {
        $actions = [];

        foreach ($this->getRecordActions() as $action) {
            $action = $action->getClone();

            if (! $action instanceof BulkAction) {
                $action->record($record);
            }

            if ($action->isHidden()) {
                continue;
            }

            $actions[] = $action;
        }

        return $actions;
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     */
    protected function getRenderRecordActionsAlignment(): Alignment | string | null
    {
        $alignment = $this->getRecordActionsAlignment();

        return filled($alignment) ? (Alignment::tryFrom($alignment) ?? $alignment) : null;
    }

    /**
     * @internal Never call or override this method. It is not part of the public API and may change in minor and patch releases.
     *
     * @param  array<Action | ActionGroup>  $recordActions
     */
    protected function generateRecordActionsHtml(array $recordActions, ?Factory $viewFactory = null): string
    {
        ob_start(); ?>
        <div
            class="<?= Arr::toCssClasses([
                'fi-ta-actions',
                match ($recordActionsAlignment = $this->getRenderRecordActionsAlignment()) {
                    Alignment::Center => 'fi-align-center',
                    Alignment::Start, Alignment::Left => 'fi-align-start',
                    Alignment::Between, Alignment::Justify => 'fi-align-between',
                    Alignment::End, Alignment::Right => '',
                    default => is_string($recordActionsAlignment) ? $recordActionsAlignment : '',
                },
            ]); ?>"
        >
            <?php foreach ($this->iterateRenderLoop($recordActions, $viewFactory) as $action) { ?>
                <?= e($action); ?>
            <?php } ?>
        </div>
        <?php return ob_get_clean();
    }
}
