<?php

namespace Filament\Tables\Columns\Concerns;

use Filament\Tables\Columns\ColumnGroup;

trait BelongsToGroup
{
    protected ?ColumnGroup $group = null;

    public function group(?ColumnGroup $group): static
    {
        $this->group = $group;

        return $this;
    }

    public function getGroup(): ?ColumnGroup
    {
        return $this->group;
    }
}
