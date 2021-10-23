<?php

namespace Filament\Tables\Columns\Concerns;

trait CanBeSearchable
{
    protected bool $isSearchable = false;

    public function searchable(bool $condition = true): static
    {
        $this->isSearchable = $condition;

        return $this;
    }

    public function isSearchable(): bool
    {
        return $this->isSearchable;
    }
}
