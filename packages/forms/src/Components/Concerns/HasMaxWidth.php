<?php

namespace Filament\Forms\Components\Concerns;

trait HasMaxWidth
{
    protected $maxWidth = null;

    public function maxWidth(string | callable $width): static
    {
        $this->maxWidth = $width;

        return $this;
    }

    public function getMaxWidth(): ?string
    {
        return $this->evaluate($this->maxWidth);
    }
}
