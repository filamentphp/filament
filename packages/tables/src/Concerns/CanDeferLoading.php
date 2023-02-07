<?php

namespace Filament\Tables\Concerns;

/**
 * @property bool $dataReadyToLoad
 */
trait CanDeferLoading
{
    public bool $dataReadyToLoad = false;

    public function isLoadingDeferred(): bool
    {
        return false;
    }

    public function isReadyToLoadData(): void
    {
        $this->dataReadyToLoad = true;
    }

    public function allowLoadingData(): void
    {
        $this->isReadyToLoadData();
    }
}
