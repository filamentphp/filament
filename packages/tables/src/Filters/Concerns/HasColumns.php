<?php

namespace Filament\Tables\Filters\Concerns;

trait HasColumns
{
    protected array | int | null $columns = null;

    public function columns(array | int | null $columns = 2): static
    {
        $this->columns = $columns;

        return $this;
    }

    public function getColumns(): array | int | null
    {
        return $this->columns;
    }
}
