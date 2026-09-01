<?php

namespace Filament\Support\Concerns;

use Closure;

trait CanOrderColumns
{
    /**
     * @var array<string | int, int | Closure | null> | null
     */
    protected ?array $columnOrder = null;

    /**
     * @param  array<string, int | Closure | null> | int | Closure | null  $order
     */
    public function columnOrder(array | int | Closure | null $order): static
    {
        if ($order instanceof Closure) {
            $this->columnOrder[] = $order;

            return $this;
        }

        if (! is_array($order)) {
            $order = [
                'lg' => $order,
            ];
        }

        $this->columnOrder = [
            ...($this->columnOrder ?? []),
            ...$order,
        ];

        return $this;
    }

    /**
     * @return array<string, ?int> | int | null
     */
    public function getColumnOrder(int | string | null $breakpoint = null): array | int | null
    {
        $order = $this->columnOrder ?? [
            'default' => null,
            'sm' => null,
            'md' => null,
            'lg' => null,
            'xl' => null,
            '2xl' => null,
        ];

        foreach ($this->columnOrder ?? [] as $columnOrderBreakpoint => $columnOrder) {
            $columnOrder = $this->evaluate($columnOrder);

            if (is_array($columnOrder)) {
                $order = [
                    ...$order,
                    ...$columnOrder,
                ];

                unset($order[$columnOrderBreakpoint]);

                continue;
            }

            if (blank($columnOrderBreakpoint)) {
                unset($order[$columnOrderBreakpoint]);

                continue;
            }

            if (! is_string($columnOrderBreakpoint)) {
                $order['default'] = $columnOrder;

                unset($order[$columnOrderBreakpoint]);

                continue;
            }

            $order[$columnOrderBreakpoint] = $columnOrder;
        }

        if ($breakpoint !== null) {
            return $this->evaluate($order[$breakpoint] ?? null);
        }

        return $order;
    }
}
