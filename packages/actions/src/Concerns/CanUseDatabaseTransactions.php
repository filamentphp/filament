<?php

namespace Filament\Actions\Concerns;

use Closure;
use Illuminate\Support\Facades\DB;

trait CanUseDatabaseTransactions
{
    protected bool | Closure $hasDatabaseTransactions = false;

    public function databaseTransaction(bool | Closure $condition = true): static
    {
        $this->hasDatabaseTransactions = $condition;

        return $this;
    }

    public function hasDatabaseTransactions(): bool
    {
        return (bool) $this->evaluate($this->hasDatabaseTransactions);
    }

    public function beginDatabaseTransaction(): void
    {
        if (! $this->hasDatabaseTransactions()) {
            return;
        }

        DB::beginTransaction();
    }

    public function commitDatabaseTransaction(): void
    {
        if (! $this->hasDatabaseTransactions()) {
            return;
        }

        DB::commit();
    }

    public function rollBackDatabaseTransaction(): void
    {
        if (! $this->hasDatabaseTransactions()) {
            return;
        }

        DB::rollBack();
    }
}
