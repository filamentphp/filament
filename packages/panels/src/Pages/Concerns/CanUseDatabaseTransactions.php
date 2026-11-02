<?php

namespace Filament\Pages\Concerns;

use Closure;
use Filament\Facades\Filament;
use Filament\Support\Components\Attributes\Unexposed;
use Illuminate\Support\Facades\DB;

trait CanUseDatabaseTransactions
{
    protected ?bool $hasDatabaseTransactions = null;

    #[Unexposed]
    public function hasDatabaseTransactions(): bool
    {
        return $this->hasDatabaseTransactions ?? Filament::getCurrentOrDefaultPanel()->hasDatabaseTransactions();
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
