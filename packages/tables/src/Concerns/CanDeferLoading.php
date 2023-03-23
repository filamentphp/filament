<?php

namespace Filament\Tables\Concerns;

trait CanDeferLoading
{
    public bool $isTableLoaded = false;

    public function isTableLoadingDeferred(): bool
    {
        return false;
    }

    public function loadTable(): void
    {
        $this->isTableLoaded = true;
    }

    public function isTableLoaded(): bool
    {
        if (! $this->isTableLoadingDeferred()) {
            return true;
        }

        return $this->isTableLoaded;
    }
}
