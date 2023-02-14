<?php

namespace Filament\Tables\Concerns;

/**
 * @property bool $tableDataReadyToLoad
 */
trait CanDeferLoading
{
    public bool $tableDataReadyToLoad = false;

    public function isTableLoadingDeferred(): bool
    {
        return false;
    }

    public function allowTableLoadingData(): void
    {
        $this->tableDataReadyToLoad = true;
    }

    public function canTableDataBeLoaded(): bool
    {
        if (! $this->isTableLoadingDeferred()) {
            return true;
        }

        return $this->tableDataReadyToLoad;
    }
}
