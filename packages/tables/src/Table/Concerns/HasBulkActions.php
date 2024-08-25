<?php

namespace Filament\Tables\Table\Concerns;

use Closure;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Enums\RecordCheckboxPosition;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use InvalidArgumentException;

trait HasBulkActions
{
    /**
     * @var array<BulkAction | ActionGroup>
     */
    protected array $bulkActions = [];

    /**
     * @var array<string, BulkAction>
     */
    protected array $flatBulkActions = [];

    protected ?Closure $checkIfRecordIsSelectableUsing = null;

    protected bool | Closure | null $selectsCurrentPageOnly = false;

    protected RecordCheckboxPosition | Closure | null $recordCheckboxPosition = null;

    protected bool | Closure | null $isSelectable = null;

    /**
     * @param  array<BulkAction | ActionGroup> | ActionGroup  $actions
     */
    public function bulkActions(array | ActionGroup $actions): static
    {
        // We must remove the existing cached bulk actions before setting the new ones, as
        // the visibility of the checkboxes is determined by which bulk actions are visible.
        // The `$this->flatBulkActions` array is used to determine if any bulk actions are
        // visible. We cannot simply clear it, as the bulk actions defined in the header
        // of the table are also stored in this array, and we do not want to remove them,
        // only the bulk actions that are stored in the `$this->bulkActions` array.
        foreach ($this->bulkActions as $existingAction) {
            if ($existingAction instanceof ActionGroup) {
                /** @var array<BulkAction> $flatExistingActions */
                $flatExistingActions = $existingAction->getFlatActions();

                $this->removeCachedBulkActions($flatExistingActions);
            } elseif ($existingAction instanceof BulkAction) {
                $this->removeCachedBulkActions([$existingAction]);
            }
        }

        $this->bulkActions = [];

        $this->pushBulkActions($actions);

        return $this;
    }

    /**
     * @param  array<BulkAction | ActionGroup> | ActionGroup  $actions
     */
    public function pushBulkActions(array | ActionGroup $actions): static
    {
        foreach (Arr::wrap($actions) as $action) {
            $action->table($this);

            if ($action instanceof ActionGroup) {
                /** @var array<string, BulkAction> $flatActions */
                $flatActions = $action->getFlatActions();

                $this->mergeCachedFlatBulkActions($flatActions);
            } elseif ($action instanceof BulkAction) {
                $this->cacheBulkAction($action);
            } else {
                throw new InvalidArgumentException('Table bulk actions must be an instance of ' . BulkAction::class . ' or ' . ActionGroup::class . '.');
            }

            $this->bulkActions[] = $action;
        }

        return $this;
    }

    /**
     * @param  array<BulkAction | ActionGroup>  $actions
     */
    public function groupedBulkActions(array $actions): static
    {
        $this->bulkActions([BulkActionGroup::make($actions)]);

        return $this;
    }

    protected function cacheBulkAction(BulkAction $action): void
    {
        $this->flatBulkActions[$action->getName()] = $action;
    }

    /**
     * @param  array<string, BulkAction>  $actions
     */
    protected function mergeCachedFlatBulkActions(array $actions): void
    {
        $this->flatBulkActions = [
            ...$this->flatBulkActions,
            ...$actions,
        ];
    }

    /**
     * @param  array<BulkAction>  $actions
     */
    protected function removeCachedBulkActions(array $actions): void
    {
        $this->flatBulkActions = array_filter(
            $this->flatBulkActions,
            fn (BulkAction $existingAction): bool => ! in_array($existingAction, $actions, true),
        );
    }

    public function checkIfRecordIsSelectableUsing(?Closure $callback): static
    {
        $this->checkIfRecordIsSelectableUsing = $callback;

        return $this;
    }

    public function selectCurrentPageOnly(bool | Closure $condition = true): static
    {
        $this->selectsCurrentPageOnly = $condition;

        return $this;
    }

    /**
     * @return array<BulkAction | ActionGroup>
     */
    public function getBulkActions(): array
    {
        return $this->bulkActions;
    }

    /**
     * @return array<string, BulkAction>
     */
    public function getFlatBulkActions(): array
    {
        return $this->flatBulkActions;
    }

    public function getBulkAction(string $name): ?BulkAction
    {
        $action = $this->getFlatBulkActions()[$name] ?? null;
        $action?->records(fn (): EloquentCollection | Collection => $this->getLivewire()->getSelectedTableRecords($action->shouldFetchSelectedRecords()));

        return $action;
    }

    public function isRecordSelectable(Model $record): bool
    {
        return (bool) ($this->evaluate(
            $this->checkIfRecordIsSelectableUsing,
            namedInjections: [
                'record' => $record,
            ],
            typedInjections: [
                Model::class => $record,
                $record::class => $record,
            ],
        ) ?? true);
    }

    public function getAllSelectableRecordsCount(): int
    {
        return $this->getLivewire()->getAllSelectableTableRecordsCount();
    }

    public function selectable(bool | Closure | null $condition = true): static
    {
        $this->isSelectable = $condition;

        return $this;
    }

    public function isSelectionEnabled(): bool
    {
        if (is_bool($isSelectable = $this->evaluate($this->isSelectable))) {
            return $isSelectable;
        }

        foreach ($this->getFlatBulkActions() as $bulkAction) {
            if ($bulkAction->isVisible()) {
                return true;
            }
        }

        return false;
    }

    public function selectsCurrentPageOnly(): bool
    {
        return (bool) $this->evaluate($this->selectsCurrentPageOnly);
    }

    public function checksIfRecordIsSelectable(): bool
    {
        return $this->checkIfRecordIsSelectableUsing !== null;
    }

    public function recordCheckboxPosition(RecordCheckboxPosition | Closure | null $position = null): static
    {
        $this->recordCheckboxPosition = $position;

        return $this;
    }

    public function getRecordCheckboxPosition(): RecordCheckboxPosition
    {
        return $this->evaluate($this->recordCheckboxPosition) ?? RecordCheckboxPosition::BeforeCells;
    }
}
