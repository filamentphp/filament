<?php

namespace Filament\Tables\Actions\Concerns;

use Closure;

trait CanFetchSelectedRecords
{
    protected bool | Closure $shouldFetchSelectedRecords = true;

    public function fetchSelectedRecords(bool | Closure $condition = true): static
    {
        $this->shouldFetchSelectedRecords = $condition;

        return $this;
    }

    public function shouldFetchSelectedRecords(): bool
    {
        return (bool) $this->evaluate($this->shouldFetchSelectedRecords);
    }
}
