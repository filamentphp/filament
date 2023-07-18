<?php

namespace Filament\Tables\Concerns;

trait CanDeferLoading
{
    public bool $isTableLoaded = false;

    /**
     * @deprecated Override the `table()` method to configure the table.
     */
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
        if (! $this->getTable()->isLoadingDeferred()) {
            return true;
        }

        return $this->isTableLoaded;
    }
}
