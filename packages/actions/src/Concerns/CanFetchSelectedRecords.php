<?php

namespace Filament\Actions\Concerns;

use Closure;

trait CanFetchSelectedRecords
{
    protected bool | Closure $shouldFetchSelectedRecords = true;

    protected int | Closure | null $selectedRecordsChunkSize = null;

    public function fetchSelectedRecords(bool | Closure $condition = true): static
    {
        $this->shouldFetchSelectedRecords = $condition;

        return $this;
    }

    public function shouldFetchSelectedRecords(): bool
    {
        return (bool) $this->evaluate($this->shouldFetchSelectedRecords);
    }

    public function chunkSelectedRecords(int | Closure | null $chunkSize = 100): static
    {
        $this->selectedRecordsChunkSize = $chunkSize;

        return $this;
    }

    public function getSelectedRecordsChunkSize(): ?int
    {
        return $this->evaluate($this->selectedRecordsChunkSize);
    }
}
