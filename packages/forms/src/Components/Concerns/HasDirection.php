<?php

namespace Filament\Forms\Components\Concerns;

use Closure;

trait HasDirection
{
    protected bool | Closure | null $isRow = null;

    protected array | int | null $rowColumns = null;

    public function row(bool | Closure $condition = true, array | int | null $columns = 2): static
    {
        $this->isRow = fn () => $condition;

        $this->rowColumns = $columns;

        return $this;
    }

    public function isRow(): bool
    {
        return (bool) $this->evaluate($this->isRow);
    }

    public function getDirection(): string
    {
        if ($this->isRow()) {
            $this->columns($this->rowColumns);

            return 'row';
        }

        return 'column';
    }
}
