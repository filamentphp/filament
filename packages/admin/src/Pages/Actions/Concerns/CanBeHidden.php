<?php

namespace Filament\Pages\Actions\Concerns;

trait CanBeHidden
{
    protected bool $isHidden = false;

    protected bool $isVisible = true;

    public function hidden(bool $condition = true): static
    {
        $this->isHidden = $condition;

        return $this;
    }

    public function visible(bool $condition = true): static
    {
        $this->isVisible = $condition;

        return $this;
    }

    public function isHidden(): bool
    {
        if ($this->isHidden) {
            return true;
        }

        return ! $this->isVisible;
    }
}
