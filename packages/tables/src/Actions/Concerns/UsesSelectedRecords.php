<?php

namespace Filament\Tables\Actions\Concerns;

trait UsesSelectedRecords
{
    protected bool $needsAccessToSelectedRecords = false;

    public function accessSelectedRecords(bool $access = true): static
    {
        $this->needsAccessToSelectedRecords = $access;

        return $this;
    }

    public function needsAccessToSelectedRecords(): bool
    {
        return $this->needsAccessToSelectedRecords;
    }
}
