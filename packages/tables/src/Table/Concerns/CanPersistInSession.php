<?php

namespace Filament\Tables\Table\Concerns;

use Closure;
use Filament\Tables\Contracts\TableStateStore;

trait CanPersistInSession
{
    protected TableStateStore | Closure | null $stateStore = null;

    public function persistInSession(bool | Closure $condition = true): static
    {
        $this->persistFiltersInSession($condition);
        $this->persistSearchInSession($condition);
        $this->persistColumnSearchesInSession($condition);
        $this->persistSortInSession($condition);
        $this->persistGroupInSession($condition);
        $this->persistColumnsInSession($condition);
        $this->persistRecordsPerPageInSession($condition);

        return $this;
    }

    public function persistStateUsing(TableStateStore | Closure | null $store): static
    {
        $this->stateStore = $store;

        return $this;
    }

    public function getStateStore(): TableStateStore
    {
        return $this->evaluate($this->stateStore) ?? app(TableStateStore::class);
    }
}
