<?php

namespace Filament\Schemas\Concerns;

use Closure;
use Filament\Schemas\Schema;

trait HasColumns
{
    /**
     * @var array<string | int, int | Closure | null> | null
     */
    protected ?array $columns = null;

    /**
     * @param  array<string, int | Closure | null> | int | Closure | null  $columns
     */
    public function columns(array | int | Closure | null $columns = 2): static
    {
        if ($columns instanceof Closure) {
            $this->columns[] = $columns;

            return $this;
        }

        if (! is_array($columns)) {
            $columns = [
                'lg' => $columns,
            ];
        }

        $this->columns = [
            ...($this->columns ?? []),
            ...$columns,
        ];

        return $this;
    }

    /**
     * @return array<string, ?int> | int | null
     */
    public function getColumns(?string $breakpoint = null): array | int | null
    {
        $columns = $this->getAllColumns();

        if ($breakpoint !== null) {
            return $columns[$breakpoint] ?? null;
        }

        return $columns;
    }

    /**
     * @return array<string, ?int>
     */
    public function getAllColumns(): array
    {
        if ($this instanceof Schema && $this->getParentComponent()) {
            return $this->getParentComponent()->getAllColumns();
        }

        $columns = $this->columns ?? [
            'default' => 1,
            'sm' => null,
            'md' => null,
            'lg' => null,
            'xl' => null,
            '2xl' => null,
        ];

        foreach ($this->columns ?? [] as $columnBreakpoint => $column) {
            $column = $this->evaluate($column);

            if (is_array($column)) {
                $columns = [
                    ...$columns,
                    ...$column,
                ];

                unset($columns[$columnBreakpoint]);

                continue;
            }

            if (blank($columnBreakpoint)) {
                unset($columns[$columnBreakpoint]);

                continue;
            }

            if (! is_string($columnBreakpoint)) {
                $columns['lg'] = $column;

                unset($columns[$columnBreakpoint]);

                continue;
            }

            $columns[$columnBreakpoint] = $column;
        }

        return $columns;
    }
}
