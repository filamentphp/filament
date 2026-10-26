<?php

namespace Filament\Tables\Concerns;

use Filament\Support\Components\Attributes\Exposed;
use Filament\Support\Components\Attributes\Unexposed;
use Livewire\Attributes\Locked;

trait CanDeferLoading
{
    #[Locked]
    public bool $isTableLoaded = false;

    /**
     * @deprecated Override the `table()` method to configure the table.
     */
    #[Unexposed]
    public function isTableLoadingDeferred(): bool
    {
        return false;
    }

    #[Exposed]
    public function loadTable(): void
    {
        $this->isTableLoaded = true;
    }

    #[Unexposed]
    public function isTableLoaded(): bool
    {
        if (! $this->getTable()->isLazyLoaded()) {
            return true;
        }

        return $this->isTableLoaded;
    }
}
