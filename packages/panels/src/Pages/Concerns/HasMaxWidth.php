<?php

namespace Filament\Pages\Concerns;

use Filament\Support\Enums\MaxWidth;

trait HasMaxWidth
{
    protected ?string $maxWidth = null;

    public function getMaxWidth(): MaxWidth | string | null
    {
        return $this->maxWidth;
    }
}
