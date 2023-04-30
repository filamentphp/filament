<?php

namespace Filament\Forms\Concerns;

use Filament\Forms\ComponentContainer;

trait HasColumns
{
    protected ?array $columns = null;

    public function columns(array | int | string | null $columns = 2): static
    {
        if (! is_array($columns)) {
            $columns = [
                'lg' => $columns,
            ];
        }

        $this->columns = array_merge($this->columns ?? [], $columns);

        return $this;
    }

    public function getColumns($breakpoint = null): array | int | string | null
    {
        $columns = $this->getColumnsConfig();

        if ($breakpoint !== null) {
            return $columns[$breakpoint] ?? null;
        }

        return $columns;
    }

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
