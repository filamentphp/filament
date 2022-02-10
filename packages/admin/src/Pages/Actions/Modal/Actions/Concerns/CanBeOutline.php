<?php

namespace Filament\Pages\Actions\Modal\Actions\Concerns;

trait CanBeOutline
{
    protected bool $isOutline = false;

    public function outline(bool $condition = true): static
    {
        $this->isOutline = $condition;

        return $this;
    }

    public function isOutline(): bool
    {
        return $this->isOutline;
    }
}
