<?php

namespace Filament\Tables\Concerns;

use Filament\Tables\Grouping\Group;
use Illuminate\Database\Eloquent\Builder;

trait CanGroupRecords
{
    public $tableGrouping = null;

    public function getTableGrouping(): ?Group
    {
        if (blank($this->tableGrouping)) {
            return $this->getTable()->hasGroups() ? null : $this->getTable()->getDefaultGroup();
        }

        return $this->getTable()->getGroup($this->tableGrouping) ?? $this->getTable()->getDefaultGroup();
    }

    public function updatedTableGroupColumn(): void
    {
        $this->resetPage();
    }

    protected function applyGroupingToTableQuery(Builder $query): Builder
    {
        if ($this->isTableReordering()) {
            return $query;
        }

        $group = $this->getTableGrouping();

        if (! $group) {
            return $query;
        }

        return $group->orderQuery($query);
    }
}
