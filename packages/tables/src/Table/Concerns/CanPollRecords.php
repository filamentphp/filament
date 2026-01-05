<?php

namespace Filament\Tables\Table\Concerns;

use Closure;

trait CanPollRecords
{
    protected string | Closure | null $pollingInterval = null;

    public function poll(string | Closure | null $interval = '10s'): static
    {
        $this->pollingInterval = $interval;

        return $this;
    }

    public function getPollingInterval(): ?string
    {
        return $this->hasMountedActions()
            ? null
            : $this->evaluate($this->pollingInterval);
    }


    protected function hasMountedActions(): bool
    {
        return
            (property_exists($this, 'mountedActions') && count($this->mountedActions) > 0) ||
            (property_exists($this, 'mountedAction') && $this->mountedAction !== null);
    }

}
