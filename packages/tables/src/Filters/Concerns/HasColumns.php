<?php

namespace Filament\Tables\Filters\Concerns;

trait HasColumns
{
    protected array | int | string | null $columns = null;

    public function columns(array | int | string | null $columns = 2): static
    {
        $this->columns = $columns;

        return $this;
    }

    public function getColumns(): array | int | string | null
    {
        return $this->columns;
    }
}
