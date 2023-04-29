<?php

namespace Filament\Tables\Table\Concerns;

use Closure;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\RecordCheckboxPosition;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use InvalidArgumentException;

trait HasBulkActions
{
    /**
     * @var array<string, BulkAction>
     */
    protected array $bulkActions = [];

    /**
     * @var array<string, BulkAction | ActionGroup>
     */
    protected array $groupedBulkActions = [];

    protected ?Closure $checkIfRecordIsSelectableUsing = null;

    protected bool | Closure | null $selectsCurrentPageOnly = false;

    protected string | Closure | null $recordCheckboxPosition = null;

    /**
     * @param  array<BulkAction | ActionGroup>  $actions
     */
    public function bulkActions(array | ActionGroup $actions): static
    {
        foreach (Arr::wrap($actions) as $index => $action) {
            if ($action instanceof ActionGroup) {
                foreach ($action->getActions() as $groupedAction) {
                    if (! $groupedAction instanceof BulkAction) {
                        throw new InvalidArgumentException('Table bulk actions must be an instance of ' . BulkAction::class . '.');
                    }

                    $groupedAction->table($this);
                    $this->registerBulkAction($groupedAction);
                }

                $action->dropdownPlacement('right-top');
                $action->grouped();
                $this->groupedBulkActions[$index] = $action;

                continue;
            }

            if (! $action instanceof BulkAction) {
                throw new InvalidArgumentException('Table bulk actions must be an instance of ' . BulkAction::class . '.');
            }

            $action->table($this);

            $this->registerBulkAction($action);
            $this->groupedBulkActions[$index] = $action;
        }

        return $this;
    }

    public function registerBulkAction(BulkAction $action): static
    {
        $this->bulkActions[$action->getName()] = $action;

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
     * @return array<string, BulkAction | ActionGroup>
     */
    public function getGroupedBulkActions(): array
    {
        return $this->groupedBulkActions;
    }

    /**
     * @return array<string, BulkAction>
     */
    public function getBulkActions(): array
    {
        return $this->bulkActions;
    }

    public function getBulkAction(string $name): ?BulkAction
    {
        $action = $this->getBulkActions()[$name] ?? null;
        $action?->records($this->getLivewire()->getSelectedTableRecords());

        return $action;
    }

    public function isRecordSelectable(Model $record): bool
    {
        return $this->evaluate(
            $this->checkIfRecordIsSelectableUsing,
            namedInjections: [
                'record' => $record,
            ],
            typedInjections: [
                Model::class => $record,
                $record::class => $record,
            ],
        ) ?? true;
    }

    public function isSelectionEnabled(): bool
    {
        return (bool) count(array_filter(
            $this->getBulkActions(),
            fn (BulkAction $action): bool => $action->isVisible(),
        ));
    }

    public function selectsCurrentPageOnly(): bool
    {
        return (bool) $this->evaluate($this->selectsCurrentPageOnly);
    }

    public function checksIfRecordIsSelectable(): bool
    {
        return $this->checkIfRecordIsSelectableUsing !== null;
    }

    public function recordCheckboxPosition(string | Closure | null $position = null): static
    {
        $this->recordCheckboxPosition = $position;

        return $this;
    }

    public function getRecordCheckboxPosition(): string
    {
        $position = $this->evaluate($this->recordCheckboxPosition);

        if (filled($position)) {
            return $position;
        }

        return RecordCheckboxPosition::BeforeCells;
    }
}
