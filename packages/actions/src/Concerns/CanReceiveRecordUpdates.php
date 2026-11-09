<?php

namespace Filament\Actions\Concerns;

use Closure;

trait CanReceiveRecordUpdates
{
    protected bool | Closure $shouldReceiveRecordUpdates = false;

    protected ?Closure $recordUpdateReceived = null;

    public function receiveRecordUpdates(bool | Closure $condition = true): static
    {
        $this->shouldReceiveRecordUpdates = $condition;

        return $this;
    }

    public function shouldReceiveRecordUpdates(): bool
    {
        return (bool) $this->evaluate($this->shouldReceiveRecordUpdates);
    }

    public function recordUpdateReceived(?Closure $callback): static
    {
        $this->recordUpdateReceived = $callback;

        return $this;
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function callRecordUpdateReceived(array $parameters = []): void
    {
        $this->evaluate($this->recordUpdateReceived, $parameters);
    }
}
