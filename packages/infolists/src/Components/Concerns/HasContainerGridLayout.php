<?php

namespace Filament\Infolists\Components\Concerns;

trait HasContainerGridLayout
{
    /**
     * @var array<string, int | null> | null
     */
    protected ?array $gridColumns = null;

    /**
     * @param  array<string, int | null> | int | null  $columns
     */
    public function grid(array | int | null $columns = 2): static
    {
        if (! is_array($columns)) {
            $columns = [
                'lg' => $columns,
            ];
        }

        $this->gridColumns = array_merge($this->gridColumns ?? [], $columns);

        return $this;
    }

    /**
     * @return array<string, int | null> | int | null
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

        if ($breakpoint !== null) {
            return $columns[$breakpoint] ?? null;
        }

        return $columns;
    }
}
