<?php

namespace Filament\Tables\Table\Concerns;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Enums\RecordCheckboxPosition;
use Illuminate\Database\Eloquent\Model;

trait HasBulkActions
{
    protected ?Closure $checkIfRecordIsSelectableUsing = null;

    protected bool | Closure | null $selectsCurrentPageOnly = false;

    protected RecordCheckboxPosition | Closure | null $recordCheckboxPosition = null;

    protected bool | Closure | null $isSelectable = null;

    protected bool | Closure $canTrackDeselectedRecords = true;

    protected string | Closure | null $currentSelectionLivewireProperty = null;

    protected int | Closure | null $maxSelectableRecords = null;

    protected bool | Closure $isSelectionDisabled = false;

    /**
     * @deprecated Use `toolbarActions()` instead.
     *
     * @param  array<Action | ActionGroup> | ActionGroup  $actions
     */
    public function bulkActions(array | ActionGroup $actions): static
    {
        $this->toolbarActions($actions);

        return $this;
    }

    /**
     * @deprecated Use `pushToolbarActions()` instead.
     *
     * @param  array<Action | ActionGroup> | ActionGroup  $actions
     */
    public function pushBulkActions(array | ActionGroup $actions): static
    {
        $this->pushToolbarActions($actions);

        return $this;
    }

    /**
     * @param  array<Action | ActionGroup>  $actions
     */
    public function groupedBulkActions(array $actions): static
    {
        $this->toolbarActions([BulkActionGroup::make($actions)]);

        return $this;
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
     * @param  Model | array<string, mixed>  $record
     */
    public function isRecordSelectable(Model | array $record): bool
    {
        return (bool) ($this->evaluate(
            $this->checkIfRecordIsSelectableUsing,
            namedInjections: [
                'record' => $record,
            ],
            typedInjections: ($record instanceof Model) ? [
                Model::class => $record,
                $record::class => $record,
            ] : [],
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

        foreach ($this->getFlatBulkActions() as $action) {
            if ($action->isVisible()) {
                return true;
            }
        }

        return false;
    }

    public function selectsCurrentPageOnly(): bool
    {
        return $this->evaluate($this->selectsCurrentPageOnly) || (! $this->hasQuery());
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

    public function trackDeselectedRecords(bool | Closure $condition = true): static
    {
        $this->canTrackDeselectedRecords = $condition;

        return $this;
    }

    public function canTrackDeselectedRecords(): bool
    {
        return (bool) $this->evaluate($this->canTrackDeselectedRecords);
    }

    public function currentSelectionLivewireProperty(string | Closure | null $property): static
    {
        $this->currentSelectionLivewireProperty = $property;

        return $this;
    }

    public function getCurrentSelectionLivewireProperty(): ?string
    {
        return $this->evaluate($this->currentSelectionLivewireProperty);
    }

    public function maxSelectableRecords(int | Closure | null $count): static
    {
        $this->maxSelectableRecords = $count;

        return $this;
    }

    public function getMaxSelectableRecords(): ?int
    {
        return $this->evaluate($this->maxSelectableRecords);
    }

    public function disabledSelection(bool | Closure $condition = true): static
    {
        $this->isSelectionDisabled = $condition;

        return $this;
    }

    public function isSelectionDisabled(): bool
    {
        return (bool) $this->evaluate($this->isSelectionDisabled);
    }

    /**
     * @deprecated Use `getToolbarActions()` instead.
     *
     * @return array<Action | ActionGroup>
     */
    public function getBulkActions(): array
    {
        return $this->getToolbarActions();
    }
}
