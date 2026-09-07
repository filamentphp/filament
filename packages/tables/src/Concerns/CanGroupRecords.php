<?php

namespace Filament\Tables\Concerns;

use Filament\Tables\Grouping\Group;
use Illuminate\Database\Eloquent\Builder;

trait CanGroupRecords
{
    public ?string $tableGrouping = null;

    public function getTableGrouping(): ?Group
    {
        if ($this->isTableReordering()) {
            return null;
        }

        if (
            filled($this->tableGrouping) &&
            ($group = $this->getTable()->getGroup((string) str($this->tableGrouping)->before(':')))
        ) {
            return $group;
        }

        if ($this->getTable()->isDefaultGroupSelectable()) {
            return null;
        }

        return $this->getTable()->getDefaultGroup();
    }

    public function updatedTableGrouping(): void
    {
        if ($this->getTable()->persistsGroupInSession()) {
            session()->put(
                $this->getTableGroupingSessionKey(),
                $this->tableGrouping,
            );
        }

        $this->resetPage();
    }

    /**
     * @deprecated Use `updatedTableGrouping()` instead.
     */
    public function updatedTableGroupColumn(): void
    {
        $this->updatedTableGrouping();
    }

    public function getTableGroupingDirection(): ?string
    {
        if (blank($this->tableGrouping)) {
            return null;
        }

        if (! str($this->tableGrouping)->contains(':')) {
            return 'asc';
        }

        return match ((string) str($this->tableGrouping)->after(':')) {
            'asc' => 'asc',
            'desc' => 'desc',
            default => null,
        };
    }

    protected function applyGroupingToTableQuery(Builder $query): Builder
    {
        $group = $this->getTableGrouping();

        if (! $group) {
            return $query;
        }

        $group->applyEagerLoading($query);

        $group->orderQuery($query, $this->getTableGroupingDirection() ?? $this->getTable()->getDefaultGroupDirection());

        return $query;
    }

    public function getTableGroupingSessionKey(): string
    {
        $table = md5($this::class);

        return "tables.{$table}_grouping";
    }
}
