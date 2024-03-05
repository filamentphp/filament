<?php

namespace Filament\Forms\Components\Concerns;

use Closure;

trait HasMaxHeight
{
    protected string | Closure | null $maxHeight = null;

    public function maxHeight(string | Closure | null $height): static
    {
        $this->maxHeight = $height;

        return $this;
    }

    public function getMaxHeight(): ?string
    {
        return $this->evaluate($this->maxHeight);
    }
}
