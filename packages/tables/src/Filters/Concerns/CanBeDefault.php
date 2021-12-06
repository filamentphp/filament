<?php

namespace Filament\Tables\Filters\Concerns;

trait CanBeDefault
{
    protected bool $isDefault = false;

    public function default(bool $condition = true): static
    {
        $this->isDefault = $condition;

        return $this;
    }

    public function isDefault(): bool
    {
        return $this->isDefault;
    }
}
