<?php

namespace Filament\Tables\Filters\Concerns;

trait BelongsToColumn
{
    protected ?string $columnName = null;

    public function columnName(?string $name): static
    {
        $this->columnName = $name;

        return $this;
    }

    public function getColumnName(): ?string
    {
        return $this->columnName;
    }

    public function isHeaderFilter(): bool
    {
        return filled($this->columnName);
    }
}
