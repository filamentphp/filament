<?php

namespace Filament\Tables\Filters\Concerns;

use Closure;

trait CanSpanColumns
{
    protected array | int | string | Closure | null $columnSpan = 1;

    public function columnSpan(array | int | string | Closure | null $span): static
    {
        $this->columnSpan = $span;

        return $this;
    }

    public function getColumnSpan(): array | int | string | Closure | null
    {
        return $this->columnSpan;
    }
}
