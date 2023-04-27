<?php

namespace Filament\Tables\Filters\Concerns;

trait HasColumns
{
    /**
     * @var array<string, int | string | null> | int | string | null
     */
    protected array | int | string | null $columns = null;

    /**
     * @param  array<string, int | string | null> | int | string | null  $columns
     */
    public function columns(array | int | string | null $columns = 2): static
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * @return array<string, int | string | null> | int | string | null
     */
    public function getColumns(): array | int | string | null
    {
        return $this->columns;
    }
}
