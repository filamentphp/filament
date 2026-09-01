<?php

namespace Filament\Tables\Filters\Concerns;

trait HasColumns
{
    /**
     * @var array<string, ?int> | int | null
     */
    protected array | int | null $columns = null;

    /**
     * @param  array<string, ?int> | int | null  $columns
     */
    public function columns(array | int | null $columns = 2): static
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * @return array<string, ?int> | int | null
     */
    public function getColumns(): array | int | null
    {
        return $this->columns;
    }
}
