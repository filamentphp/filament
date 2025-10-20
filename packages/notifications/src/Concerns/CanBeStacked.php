<?php

namespace Filament\Notifications\Concerns;

trait CanBeStacked
{
    protected bool $isStacked = false;

    public function stacked(bool $condition = true): static
    {
        $this->isStacked = $condition;

        return $this;
    }

    public function isStacked(): bool
    {
        return $this->isStacked;
    }
}
