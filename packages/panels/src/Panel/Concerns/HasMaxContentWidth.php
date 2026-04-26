<?php

namespace Filament\Panel\Concerns;

use Filament\Support\Enums\Width;
use Closure;

trait HasMaxContentWidth
{
    protected Width | Closure | string | null $maxContentWidth = null;

    protected Width | string | null $simplePageMaxContentWidth = null;

    public function maxContentWidth(Width | Closure | string | null $maxContentWidth): static
    {
        $this->maxContentWidth = $maxContentWidth;

        return $this;
    }

    public function getMaxContentWidth(): Width | string | null
    {
        return $this->evaluate($this->maxContentWidth);
    }

    public function simplePageMaxContentWidth(Width | string | null $width): static
    {
        $this->simplePageMaxContentWidth = $width;

        return $this;
    }

    public function getSimplePageMaxContentWidth(): Width | string | null
    {
        return $this->simplePageMaxContentWidth;
    }
}
