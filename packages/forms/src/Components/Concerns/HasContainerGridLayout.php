<?php

namespace Filament\Forms\Components\Concerns;

trait HasContainerGridLayout
{
    /**
     * @var array<string, int | string | null> | null
     */
    protected ?array $gridColumns = null;

    /**
     * @param  array<string, int | string | null> | int | string | null  $columns
     */
    public function grid(array | int | string | null $columns = 2): static
    {
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
     * @return array<string, int | string | null> | int | string | null
     */
    public function getGridColumns(?string $breakpoint = null): array | int | string | null
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
