<?php

namespace Filament\Panel\Concerns;

trait HasMaxContentWidth
{
    protected ?string $maxContentWidth = null;

    public function maxContentWidth(?string $maxContentWidth): static
    {
        $this->maxContentWidth = $maxContentWidth;

        return $this;
    }

    public function getMaxContentWidth(): ?string
    {
        return $this->maxContentWidth;
    }
}
