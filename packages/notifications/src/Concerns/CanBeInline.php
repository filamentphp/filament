<?php

namespace Filament\Notifications\Concerns;

trait CanBeInline
{
    protected bool $isInline = false;

    public function inline(bool $condition = true): static
    {
        $this->isInline = $condition;

        return $this;
    }

    public function isInline(): bool
    {
        return $this->isInline;
    }
}
