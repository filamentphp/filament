<?php

namespace Filament\Forms\Concerns;

trait HasGrid
{
    protected ?array $grid = null;

    public function grid(array | int | null $grid = 2): static
    {
        if (! is_array($grid)) {
            $grid = [
                'lg' => $grid,
            ];
        }

        $this->grid = array_merge($this->grid ?? [], $grid);

        return $this;
    }

    public function getGrid($breakpoint = null): array | int | null
    {
        $grid = $this->getGridConfig();

        if ($breakpoint !== null) {
            return $grid[$breakpoint] ?? null;
        }

        return $grid;
    }

    public function getGridConfig(): array
    {
        return $this->grid ?? [
            'default' => 1,
            'sm' => null,
            'md' => null,
            'lg' => null,
            'xl' => null,
            '2xl' => null,
        ];
    }
}
