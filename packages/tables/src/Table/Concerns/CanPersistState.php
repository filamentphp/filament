<?php

namespace Filament\Tables\Table\Concerns;

use Closure;
use Filament\Tables\Contracts\TablePersistenceStore;
use Filament\Tables\Persistence\SessionTablePersistenceStore;

trait CanPersistState
{
    protected TablePersistenceStore | Closure | null $persistenceStore = null;

    /**
     * @var array<string, TablePersistenceStore>
     */
    protected array $persistenceStoreCache = [];

    public function persistenceStore(TablePersistenceStore | Closure | null $persistenceStore): static
    {
        $this->persistenceStore = $persistenceStore;
        $this->persistenceStoreCache = [];

        return $this;
    }

    public function getPersistenceStore(): TablePersistenceStore
    {
        return $this->persistenceStoreCache['default'] ??= $this->evaluate($this->persistenceStore) ?? app(SessionTablePersistenceStore::class);
    }

    protected function resolvePersistenceStore(string $feature, TablePersistenceStore | Closure | null $persistenceStore): TablePersistenceStore
    {
        return $this->persistenceStoreCache[$feature] ??= $this->evaluate($persistenceStore) ?? $this->getPersistenceStore();
    }

    public function persistInStore(bool | Closure $condition = true, TablePersistenceStore | Closure | null $persistenceStore = null): static
    {
        $this->persistFiltersInStore($condition, $persistenceStore);
        $this->persistSearchInStore($condition, $persistenceStore);
        $this->persistColumnSearchesInStore($condition, $persistenceStore);
        $this->persistSortInStore($condition, $persistenceStore);
        $this->persistGroupInStore($condition, $persistenceStore);
        $this->persistColumnsInStore($condition, $persistenceStore);
        $this->persistRecordsPerPageInStore($condition, $persistenceStore);

        return $this;
    }

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
}
