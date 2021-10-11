<?php

namespace Filament\Tables\Concerns;

trait CanReorderRecords
{
    public bool $isReorderable = true;

    public function isReorderable(): bool
    {
        return $this->isReorderable && $this->getTable()->isReorderable();
    }

    public function reorder($order): void
    {
        $this->getTable()->reorder($order);
    }
}
