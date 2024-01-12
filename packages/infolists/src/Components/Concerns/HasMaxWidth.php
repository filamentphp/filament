<?php

namespace Filament\Infolists\Components\Concerns;

use Closure;
use Filament\Support\Enums\MaxWidth;

trait HasMaxWidth
{
    protected MaxWidth | string | Closure | null $maxWidth = null;

    public function maxWidth(MaxWidth | string | Closure | null $width): static
    {
        $this->maxWidth = $width;

        return $this;
    }

    public function getMaxWidth(): MaxWidth | string | null
    {
        return $this->evaluate($this->maxWidth);
    }
}
