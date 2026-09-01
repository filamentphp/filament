<?php

namespace Filament\Support\Concerns;

use Closure;

trait CanSpanColumns
{
    /**
     * @var array<string | int, int | string | Closure | null> | null
     */
    protected ?array $columnSpan = null;

    /**
     * @var array<string | int, int | string | Closure | null> | null
     */
    protected ?array $columnStart = null;

    /**
     * @param  array<string, int | string | Closure | null> | int | string | Closure | null  $span
     */
    public function columnSpan(array | int | string | Closure | null $span): static
    {
        if ($span instanceof Closure) {
            $this->columnSpan[] = $span;

            return $this;
        }

        if (! is_array($span)) {
            $span = [
                'default' => 1,
                'lg' => $span,
            ];
        }

        $this->columnSpan = [
            ...($this->columnSpan ?? []),
            ...$span,
        ];

        return $this;
    }

    public function columnSpanFull(): static
    {
        $this->columnSpan(['default' => 'full']);

        return $this;
    }

    /**
     * @param  array<string, int | string | Closure | null> | int | string | Closure | null  $start
     */
    public function columnStart(array | int | string | Closure | null $start): static
    {
        if ($start instanceof Closure) {
            $this->columnStart[] = $start;

            return $this;
        }

        if (! is_array($start)) {
            $start = [
                'lg' => $start,
            ];
        }

        $this->columnStart = [
            ...($this->columnStart ?? []),
            ...$start,
        ];

        return $this;
    }

    /**
     * @return array<string, int | string | null> | int | string | null
     */
    public function getColumnSpan(int | string | null $breakpoint = null): array | int | string | null
    {
        $span = $this->columnSpan ?? [
            'default' => 1,
            'sm' => null,
            'md' => null,
            'lg' => null,
            'xl' => null,
            '2xl' => null,
        ];

        foreach ($this->columnSpan ?? [] as $columnSpanBreakpoint => $columnSpan) {
            $columnSpan = $this->evaluate($columnSpan);

            if (is_array($columnSpan)) {
                $span = [
                    ...$span,
                    ...$columnSpan,
                ];

                unset($span[$columnSpanBreakpoint]);

                continue;
            }

            if (blank($columnSpanBreakpoint)) {
                unset($span[$columnSpanBreakpoint]);

                continue;
            }

            if (! is_string($columnSpanBreakpoint)) {
                $span['default'] = $columnSpan;

                unset($span[$columnSpanBreakpoint]);

                continue;
            }

            $span[$columnSpanBreakpoint] = $columnSpan;
        }

        if ($breakpoint !== null) {
            return $this->evaluate($span[$breakpoint] ?? null);
        }

        return $span;
    }

    /**
     * @return array<string, int | string | null> | int | string | null
     */
    public function getColumnStart(int | string | null $breakpoint = null): array | int | string | null
    {
        $start = $this->columnStart ?? [
            'default' => null,
            'sm' => null,
            'md' => null,
            'lg' => null,
            'xl' => null,
            '2xl' => null,
        ];

        foreach ($this->columnStart ?? [] as $columnStartBreakpoint => $columnStart) {
            $columnStart = $this->evaluate($columnStart);

            if (is_array($columnStart)) {
                $start = [
                    ...$start,
                    ...$columnStart,
                ];

                unset($start[$columnStartBreakpoint]);

                continue;
            }

            if (blank($columnStartBreakpoint)) {
                unset($start[$columnStartBreakpoint]);

                continue;
            }

            if (! is_string($columnStartBreakpoint)) {
                $start['default'] = $columnStart;

                unset($start[$columnStartBreakpoint]);

                continue;
            }

            $start[$columnStartBreakpoint] = $columnStart;
        }

        if ($breakpoint !== null) {
            return $this->evaluate($start[$breakpoint] ?? null);
        }

        return $start;
    }
}
