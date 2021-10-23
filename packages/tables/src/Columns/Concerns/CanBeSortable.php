<?php

namespace Filament\Tables\Columns\Concerns;

trait CanBeSortable
{
    protected bool $isSortable = false;

    public function sortable(bool $condition = true): static
    {
        $this->isSortable = $condition;

        return $this;
    }

    public function isSortable(): bool
    {
        return $this->isSortable;
    }
}
