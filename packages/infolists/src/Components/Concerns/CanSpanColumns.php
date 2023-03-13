<?php

namespace Filament\Infolists\Components\Concerns;

use Closure;

trait CanSpanColumns
{
    /**
     * @var array<string, int | string | Closure | null>
     */
    protected array $columnSpan = [
        'default' => 1,
        'sm' => null,
        'md' => null,
        'lg' => null,
        'xl' => null,
        '2xl' => null,
    ];

    /**
     * @var array<string, int | string | Closure | null>
     */
    protected array $columnStart = [
        'default' => null,
        'sm' => null,
        'md' => null,
        'lg' => null,
        'xl' => null,
        '2xl' => null,
    ];

    /**
     * @param  array<string, int | string | Closure | null> | int | string | Closure | null  $span
     */
    public function columnSpan(array | int | string | Closure | null $span): static
    {
        if (! is_array($span)) {
            $span = [
                'default' => $span,
            ];
        }

        $this->columnSpan = array_merge($this->columnSpan, $span);

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
        if (! is_array($start)) {
            $start = [
                'default' => $start,
            ];
        }

        $this->columnStart = array_merge($this->columnStart, $start);

        return $this;
    }

    /**
     * @return array<string, int | string | Closure | null> | int | string | null
     */
    public function getColumnSpan(int | string | null $breakpoint = null): array | int | string | null
    {
        $span = $this->columnSpan;

        if ($breakpoint !== null) {
            return $this->evaluate($span[$breakpoint] ?? null);
        }

        return array_map(
            fn (array | int | string | Closure | null $value): array | int | string | null => $this->evaluate($value),
            $span,
        );
    }

    /**
     * @return array<string, int | string | Closure | null> | int | string | null
     */
    public function getColumnStart(int | string | null $breakpoint = null): array | int | string | null
    {
        $start = $this->columnStart;

        if ($breakpoint !== null) {
            return $this->evaluate($start[$breakpoint] ?? null);
        }

        return array_map(
            fn (array | int | string | Closure | null $value): array | int | string | null => $this->evaluate($value),
            $start,
        );
    }
}
