<?php

namespace Filament\Panel\Concerns;

use Closure;
use Filament\Support\Enums\Width;

trait HasMaxContentWidth
{
    protected Width | string | Closure | null $maxContentWidth = null;

    protected Width | string | Closure | null $simplePageMaxContentWidth = null;

    public function maxContentWidth(Width | string | Closure | null $maxContentWidth): static
    {
        $this->maxContentWidth = $maxContentWidth;

        return $this;
    }

    public function getMaxContentWidth(): Width | string | null
    {
        return $this->evaluate($this->maxContentWidth);
    }

    public function simplePageMaxContentWidth(Width | string | Closure | null $width): static
    {
        $this->simplePageMaxContentWidth = $width;

        return $this;
    }

    public function getSimplePageMaxContentWidth(): Width | string | null
    {
        return $this->evaluate($this->simplePageMaxContentWidth);
    }
}
