<?php

namespace Filament\Resources;

use Filament\Forms\Components\Component;

class Form
{
    protected array | int | null $columns = [
        'md' => 2,
    ];

    protected array | Component $schema = [];

    public static function make(): static
    {
        return new static();
    }

    public function columns(array | int | null $columns): static
    {
        $this->columns = $columns;

        return $this;
    }

    public function schema(array | Component $schema): static
    {
        $this->schema = $schema;

        return $this;
    }

    public function getColumns(): array | int | null
    {
        return $this->columns;
    }

    public function getSchema(): array | Component
    {
        return $this->schema;
    }
}
