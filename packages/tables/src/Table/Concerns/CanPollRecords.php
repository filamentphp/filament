<?php

namespace Filament\Tables\Table\Concerns;

use Closure;

trait CanPollRecords
{
    protected string | Closure | null $pollingInterval = null;

    protected string | Closure | null $pollingAction = null;

    public function poll(string | Closure | null $interval = '10s'): static
    {
        $this->pollingInterval = $interval;

        return $this;
    }

    public function pollAction(string | Closure $action): static
    {
        $this->pollingAction = $action;

        return $this;
    }

    public function getPollingInterval(): ?string
    {
        return $this->evaluate($this->pollingInterval);
    }

    public function getPollingAction(): ?string
    {
        return $this->evaluate($this->pollingAction);
    }
}
