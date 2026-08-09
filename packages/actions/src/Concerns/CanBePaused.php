<?php

namespace Filament\Actions\Concerns;

use Closure;
use Filament\Support\Exceptions\Pause;

trait CanBePaused
{
    /**
     * @var array<bool | Closure>
     */
    protected array $pauseConditions = [];

    /**
     * Conditions accumulate instead of replacing each other, so that a plugin can
     * pause an action without overwriting a condition that the app registered, and
     * vice versa.
     */
    public function pauseWhen(bool | Closure $condition = true): static
    {
        $this->pauseConditions[] = $condition;

        return $this;
    }

    public function shouldPause(): bool
    {
        foreach ($this->pauseConditions as $condition) {
            if ($this->evaluate($condition)) {
                return true;
            }
        }

        return false;
    }

    public function pause(bool $shouldRollBackDatabaseTransaction = false): void
    {
        throw (new Pause)->rollBackDatabaseTransaction($shouldRollBackDatabaseTransaction);
    }
}
