<?php

namespace Filament\Widgets\Concerns;

use Closure;

trait CanPoll
{
    protected ?string $pollingInterval = '5s';

    protected ?Closure $pollingIntervalClosure = null;

    public function poll(string | Closure | null $interval = '5s'): static
    {
        if ($interval instanceof Closure) {
            $this->pollingIntervalClosure = $interval;

            return $this;
        }

        $this->pollingInterval = $interval;
        $this->pollingIntervalClosure = null;

        return $this;
    }

    public function getPollingInterval(): ?string
    {
        return $this->evaluate($this->pollingIntervalClosure ?? $this->pollingInterval);
    }
}
