<?php

namespace Filament\Forms\Components\Concerns;

use Closure;

trait HasMinHeight
{
    protected string | Closure | null $minHeight = '11.25rem';

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
