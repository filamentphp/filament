<?php

namespace Filament\Tables\Concerns;

use Filament\Support\Attributes\Exposed;
use Filament\Support\Attributes\ServerOnly;

trait CanDeferLoading
{
    public bool $isTableLoaded = false;

    /**
     * @deprecated Override the `table()` method to configure the table.
     */
    #[ServerOnly]
    public function isTableLoadingDeferred(): bool
    {
        return false;
    }

    #[Exposed]
    public function loadTable(): void
    {
        $this->isTableLoaded = true;
    }

    #[ServerOnly]
    public function isTableLoaded(): bool
    {
        if (! $this->getTable()->isLoadingDeferred()) {
            return true;
        }

        return $this->isTableLoaded;
    }
}
