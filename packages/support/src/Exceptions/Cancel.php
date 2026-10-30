<?php

namespace Filament\Support\Exceptions;

use Exception;

class Cancel extends Exception
{
    protected bool $shouldRollBackDatabaseTransaction = false;

    public function rollBackDatabaseTransaction(bool $condition = true): static
    {
        $this->shouldRollBackDatabaseTransaction = $condition;

        return $this;
    }

    public function shouldRollBackDatabaseTransaction(): bool
    {
        return $this->shouldRollBackDatabaseTransaction;
    }
}
