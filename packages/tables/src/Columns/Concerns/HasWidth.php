<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;

trait HasWidth
{
    protected int | string | Closure | null $width = null;

    public function width(int | string | Closure | null $width): static
    {
        $this->width = $width;

        return $this;
    }

    public function getWidth(): ?string
    {
        $width = $this->evaluate($this->width);

        if (is_int($width)) {
            $width = "{$width}px";
        }

        return $width;
    }
}
