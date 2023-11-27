<?php

namespace Filament\Panel\Concerns;

use Filament\Support\Enums\MaxWidth;

trait HasMaxContentWidth
{
    protected MaxWidth | string | null $maxContentWidth = null;

    public function maxContentWidth(MaxWidth | string | null $maxContentWidth): static
    {
        $this->maxContentWidth = $maxContentWidth;

        return $this;
    }

    public function getMaxContentWidth(): MaxWidth | string | null
    {
        return $this->maxContentWidth;
    }
}
