<?php

namespace Filament\Widgets\Concerns;

trait CanPoll
{
    protected ?string $pollingInterval = '5s';

    protected function getPollingInterval(): ?string
    {
        return $this->pollingInterval;
    }
}
