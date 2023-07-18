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
     * @var array<string, int | string | Closure | null> | int | string | Closure | null
     */
    protected array | int | string | Closure | null $columnStart = null;

    /**
     * @param  array<string, int | string | Closure | null> | int | string | Closure | null  $span
     */
    public function columnSpan(array | int | string | Closure | null $span): static
    {
        $this->columnSpan = $span;

        return $this;
    }

    public function columnSpanFull(): static
    {
        $this->columnSpan('full');

        return $this;
    }

    /**
     * @param  array<string, int | string | Closure | null> | int | string | Closure | null  $start
     */
    public function columnStart(array | int | string | Closure | null $start): static
    {
        $this->columnStart = $start;

        return $this;
    }

    /**
     * @return array<string, int | string | Closure | null> | int | string | Closure | null
     */
    public function getColumnSpan(): array | int | string | Closure | null
    {
        return $this->columnSpan;
    }

    /**
     * @return array<string, int | string | Closure | null> | int | string | Closure | null
     */
    public function getColumnStart(): array | int | string | Closure | null
    {
        return $this->columnStart;
    }
}
