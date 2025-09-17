<?php

namespace Filament\Schemas\Components\Concerns;

use Closure;

trait HasContainerGridLayout
{
    /**
     * @var array<string | int, int | Closure | null> | null
     */
    protected ?array $gridColumns = null;

    /**
     * @param  array<string, int | Closure | null> | int | Closure | null  $columns
     */
    public function grid(array | int | Closure | null $columns = 2): static
    {
        if ($columns instanceof Closure) {
            $this->gridColumns[] = $columns;

            return $this;
        }

        if (! is_array($columns)) {
            $columns = [
                'lg' => $columns,
            ];
        }

        $this->gridColumns = [
            ...($this->gridColumns ?? []),
            ...$columns,
        ];

        return $this;
    }

    /**
     * @return array<string, ?int> | int | null
     */
    public function getGridColumns(?string $breakpoint = null): array | int | null
    {
        $columns = $this->gridColumns ?? [
            'default' => 1,
            'sm' => null,
            'md' => null,
            'lg' => null,
            'xl' => null,
            '2xl' => null,
        ];

        foreach ($this->gridColumns ?? [] as $gridColumnBreakpoint => $gridColumn) {
            $gridColumn = $this->evaluate($gridColumn);

            if (is_array($gridColumn)) {
                $columns = [
                    ...$columns,
                    ...$gridColumn,
                ];

                unset($columns[$gridColumnBreakpoint]);

                continue;
            }

            if (blank($gridColumnBreakpoint)) {
                unset($columns[$gridColumnBreakpoint]);

                continue;
            }

            if (! is_string($gridColumnBreakpoint)) {
                $columns['lg'] = $gridColumn;

                unset($columns[$gridColumnBreakpoint]);

                continue;
            }

            $columns[$gridColumnBreakpoint] = $gridColumn;
        }

        if ($breakpoint !== null) {
            return $columns[$breakpoint] ?? null;
        }

        return $columns;
    }
}
