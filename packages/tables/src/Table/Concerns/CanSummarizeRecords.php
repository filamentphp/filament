<?php

namespace Filament\Tables\Table\Concerns;

trait CanSummarizeRecords
{
    protected bool $hasSummary = false;

    public function hasSummary(): bool
    {
        return $this->hasSummary;
    }
}
