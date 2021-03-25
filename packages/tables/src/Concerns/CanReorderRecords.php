<?php

namespace Filament\Tables\Concerns;

trait CanReorderRecords
{
    public $isReorderable = true;

    public function isReorderable()
    {
        return $this->isReorderable && $this->getTable()->isReorderable();
    }

    public function reorder($order)
    {
        return $this->getTable()->reorder($order);
    }
}
