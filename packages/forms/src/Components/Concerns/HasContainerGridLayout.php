<?php

namespace Filament\Forms\Components\Concerns;

trait HasContainerGridLayout
{
    protected ?array $gridColumns = null;

    public function grid(array | int | string | null $columns = 2): static
    {
        if (! is_array($columns)) {
            $columns = [
                'lg' => $columns,
            ];
        }

        $this->gridColumns = array_merge($this->gridColumns ?? [], $columns);

        return $this;
    }

    public function getGridColumns($breakpoint = null): array | int | string | null
    {
        $columns = $this->gridColumns ?? [
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
