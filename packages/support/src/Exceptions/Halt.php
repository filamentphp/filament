<?php

namespace Filament\Support\Exceptions;

use Exception;

class Halt extends Exception
{
    protected bool $shouldRollbackDatabaseTransaction = false;

    public function rollBackDatabaseTransaction(bool $condition = true): static
    {
        $this->shouldRollbackDatabaseTransaction = $condition;

        return $this;
    }

    public function shouldRollbackDatabaseTransaction(): bool
    {
        return $this->shouldRollbackDatabaseTransaction;
    }
}
