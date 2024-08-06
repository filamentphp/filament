<?php

namespace Filament\Panel\Concerns;

trait HasFocusMode
{
    protected bool $hasFocusMode = false;

    public function focusMode(bool $condition = true): static
    {
        $this->hasFocusMode = $condition;

        return $this;
    }

    public function hasFocusMode(): bool
    {
        return $this->hasFocusMode;
    }

}