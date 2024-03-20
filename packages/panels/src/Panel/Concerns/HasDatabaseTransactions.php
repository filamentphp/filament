<?php

namespace Filament\Panel\Concerns;

use Closure;

trait HasDatabaseTransactions
{
    protected bool | Closure $hasDatabaseTransactions = false;

    public function databaseTransactions(bool | Closure $condition = true): static
    {
        $this->hasDatabaseTransactions = $condition;

        return $this;
    }

    public function hasDatabaseTransactions(): bool
    {
        return (bool) $this->evaluate($this->hasDatabaseTransactions);
    }
}
