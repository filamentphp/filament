<?php

namespace Filament\Forms\Components\Concerns;

use Closure;

trait CanAssignColumnOrder
{
    protected array $columnOrder = [
        'default' => null,
        'sm' => null,
        'md' => null,
        'lg' => null,
        'xl' => null,
        '2xl' => null,
    ];

    public function columnOrder(array | int | string | Closure | null $order): static
    {
        if (! is_array($order)) {
            $order = [
                'default' => $order,
            ];
        }

        $this->columnOrder = array_merge($this->columnOrder, $order);

        return $this;
    }

    public function columnOrderFirst(): static
    {
        $this->columnSpan('first');

        return $this;
    }

    public function columnOrderLast(): static
    {
        $this->columnSpan('last');

        return $this;
    }

    public function getColumnOrder(int | string | null $breakpoint = null): array | int | string | null
    {
        $order = $this->columnOrder;

        if ($breakpoint !== null) {
            return $this->evaluate($order[$breakpoint] ?? null);
        }

        return array_map(
            fn (array | int | string | Closure | null $value): array | int | string | null => $this->evaluate($value),
            $order,
        );
    }
}
