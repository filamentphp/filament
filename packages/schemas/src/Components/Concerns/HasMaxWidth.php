<?php

namespace Filament\Schemas\Components\Concerns;

use Closure;
use Filament\Support\Enums\Width;

trait HasMaxWidth
{
    protected Width | string | Closure | null $maxWidth = null;

    public function maxWidth(Width | string | Closure | null $width): static
    {
        $this->maxWidth = $width;

        return $this;
    }

    public function getMaxWidth(): Width | string | null
    {
        $width = $this->evaluate($this->maxWidth);

        if (blank($width)) {
            return null;
        }

        if (is_string($width)) {
            $width = Width::tryFrom($width) ?? $width;
        }

        return $width;
    }
}
