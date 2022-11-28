<?php

namespace Filament\Tables\Filters\Concerns;

use Closure;

trait CanSpanColumns
{
    /**
     * @var array<string, int | string | Closure | null> | int | string | Closure | null
     */
    protected array | int | string | Closure | null $columnSpan = 1;

    /**
     * @param  array<string, int | string | Closure | null> | int | string | Closure | null  $span
     */
    public function columnSpan(array | int | string | Closure | null $span): static
    {
        $this->columnSpan = $span;

        return $this;
    }

    /**
     * @return array<string, int | string | Closure | null> | int | string | Closure | null
     */
    public function getColumnSpan(): array | int | string | Closure | null
    {
        return $this->columnSpan;
    }
}
