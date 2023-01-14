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
}
