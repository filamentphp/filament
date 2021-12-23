<?php

namespace Filament\Resources;

use Closure;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Grid;

class Form
{
    protected array | int | null $columns = null;

    protected array | Component | Closure $schema = [];

    final public function __construct()
    {
    }

    public static function make(): static
    {
        return new static();
    }

    public function columns(array | int | null $columns): static
    {
        $this->columns = $columns;

        return $this;
    }

    public function schema(array | Component | Closure $schema): static
    {
        $this->schema = $schema;

        return $this;
    }

    public function getColumns(): array | int | null
    {
        return $this->columns;
    }

    public function getSchema(): array
    {
        $schema = $this->schema;

        if (is_array($schema) || $schema instanceof Closure) {
            $schema = Grid::make()
                ->schema($schema)
                ->columns($this->getColumns());
        }

        return [$schema];
    }
}
