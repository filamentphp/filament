<?php

namespace Filament\Forms\Components;

class GridRepeater extends Repeater
{
    protected string $view = 'forms::components.grid-repeater';

    public function getColumns($breakpoint = null): array | int | null
    {
        $columns = $this->getColumnsConfig();

        $this->columns = null;

        if ($breakpoint !== null) {
            return $columns[$breakpoint] ?? null;
        }

        return $columns;
    }

    public function getColumnsConfig(): array
    {
        return $this->columns ?? [];
    }
}
