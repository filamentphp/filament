<?php

namespace Filament\Forms\Components\Concerns;

use Closure;

trait HasMinHeight
{
    protected string | Closure | null $minHeight = '3rem';

    public function minHeight(string | Closure | null $height): static
    {
        $this->minHeight = $height;

        return $this;
    }

    public function getMinHeight(): ?string
    {
        return $this->evaluate($this->minHeight);
    }
}
