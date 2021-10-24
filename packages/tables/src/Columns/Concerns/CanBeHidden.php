<?php

namespace Filament\Tables\Columns\Concerns;

trait CanBeHidden
{
    protected ?string $hiddenFrom = null;

    protected ?string $visibleFrom = null;

    public function hiddenFrom(string $breakpoint): static
    {
        $this->hiddenFrom = $breakpoint;

        return $this;
    }

    public function visibleFrom(string $breakpoint): static
    {
        $this->visibleFrom = $breakpoint;

        return $this;
    }

    public function getHiddenFrom(): ?string
    {
        return $this->hiddenFrom;
    }

    public function getVisibleFrom(): ?string
    {
        return $this->visibleFrom;
    }
}
