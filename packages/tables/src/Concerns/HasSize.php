<?php

namespace Filament\Tables\Concerns;

trait HasSize
{
    public function getSize(): ?string
    {
        return $this->getTableSize();
    }
}
