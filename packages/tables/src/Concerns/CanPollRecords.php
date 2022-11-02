<?php

namespace Filament\Tables\Concerns;

trait CanPollRecords
{
    protected function getTablePollingInterval(): ?string
    {
        return null;
    }
}
