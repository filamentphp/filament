<?php

namespace Filament\Widgets\Concerns;

use Closure;

trait CanPoll
{
    protected string | Closure | null $pollingInterval = '5s';

    public function poll(string | Closure | null $interval = '5s'): static
    {
        $this->pollingInterval = $interval;

        return $this;
    }

    public function getPollingInterval(): ?string
    {
        return $this->evaluate($this->pollingInterval);
    }
}
