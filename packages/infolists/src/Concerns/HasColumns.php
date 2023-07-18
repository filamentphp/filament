<?php

namespace Filament\Infolists\Concerns;

use Filament\Infolists\ComponentContainer;

trait HasColumns
{
    /**
     * @var array<string, int | null> | null
     */
    protected ?array $columns = null;

    /**
     * @param  array<string, int | null> | int | null  $columns
     */
    public function columns(array | int | null $columns = 2): static
    {
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
     * @return array<string, int | null> | int | null
     */
    public function getColumns(?string $breakpoint = null): array | int | null
    {
        $columns = $this->getColumnsConfig();

        if ($breakpoint !== null) {
            return $columns[$breakpoint] ?? null;
        }

        return $columns;
    }

    /**
     * @return array<string, int | null>
     */
    public function getColumnsConfig(): array
    {
        if ($this instanceof ComponentContainer && $this->getParentComponent()) {
            return $this->getParentComponent()->getColumnsConfig();
        }

        return $this->columns ?? [
            'default' => 1,
            'sm' => null,
            'md' => null,
            'lg' => null,
            'xl' => null,
            '2xl' => null,
        ];
    }
}
