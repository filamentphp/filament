<?php

namespace Filament\Pages\Concerns;

use Closure;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\DB;

trait CanUseDatabaseTransactions
{
    protected ?bool $hasDatabaseTransactions = null;

    public function hasDatabaseTransactions(): bool
    {
        return $this->hasDatabaseTransactions ?? Filament::getCurrentPanel()->hasDatabaseTransactions();
    }

    protected function beginDatabaseTransaction(): void
    {
        if (! $this->hasDatabaseTransactions()) {
            return;
        }

        DB::beginTransaction();
    }

    protected function commitDatabaseTransaction(): void
    {
        if (! $this->hasDatabaseTransactions()) {
            return;
        }

        DB::commit();
    }

    protected function rollBackDatabaseTransaction(): void
    {
        if (! $this->hasDatabaseTransactions()) {
            return;
        }

        DB::rollBack();
    }

    protected function wrapInDatabaseTransaction(Closure $callback): mixed
    {
        if (! $this->hasDatabaseTransactions()) {
            return $callback();
        }

        /** @phpstan-ignore-next-line */
        return DB::transaction($callback);
    }
}
