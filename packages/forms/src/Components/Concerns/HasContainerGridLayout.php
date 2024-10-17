<?php

namespace Filament\Forms\Components\Concerns;

use Closure;

trait HasContainerGridLayout
{
    /**
     * @var array<string, int | string | null> | int | string | Closure | null
     */
    protected array | int | string | Closure | null $gridColumns = null;

    /**
     * @param  array<string, int | string | null> | int | string | Closure | null  $columns
     */
    public function grid(array | int | string | Closure | null $columns = 2): static
    {
        if (is_string($columns) && $columns === 'auto') {
            $this->gridColumns = fn ($component) => count($component->getState());

            return $this;
        }

        $this->gridColumns = $columns;

        return $this;
    }

    /**
     * @return array<string, int | string | null> | int | string | null
     */
    public function getGridColumns(?string $breakpoint = null): array | int | string | null
    {
        $gridColumns = $this->evaluate($this->gridColumns);

        if (isset($gridColumns) && ! is_array($gridColumns)) {
            $gridColumns = [
                'lg' => $gridColumns,
            ];
        }

        $columns = $gridColumns ?? [
            'default' => 1,
            'sm' => null,
            'md' => null,
            'lg' => null,
            'xl' => null,
            '2xl' => null,
        ];

        if ($breakpoint !== null) {
            return $columns[$breakpoint] ?? null;
        }

        return $columns;
    }
}
