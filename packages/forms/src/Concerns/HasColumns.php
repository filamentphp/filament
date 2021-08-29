<?php

namespace Filament\Forms\Concerns;

use Filament\Forms\ComponentContainer;

trait HasColumns
{
    protected array $columns = [
        'default' => 1,
        'sm' => null,
        'md' => null,
        'lg' => null,
        'xl' => null,
        '2xl' => null,
    ];

    public function columns(array | int | null $columns = 2): static
    {
        if (! is_array($columns)) {
            $columns = [
                'lg' => $columns,
            ];
        }

        $this->columns = array_merge($this->columns, $columns);

        return $this;
    }

    public function getColumns($breakpoint = null): array | int | null
    {
        if ($this instanceof ComponentContainer && $this->getParentComponent()) {
            $columns = $this->getParentComponent()->getColumns();
        } else {
            $columns = $this->columns;
        }

        if ($breakpoint !== null) {
            $columns = $columns[$breakpoint] ?? null;
        }

        return $columns;
    }
}
