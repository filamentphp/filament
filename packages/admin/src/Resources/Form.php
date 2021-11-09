<?php

namespace Filament\Resources;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\Grid;
use Filament\Forms\Contracts\HasForms;

class Form
{
    protected array | int | null $columns = [
        'md' => 2,
    ];

    protected array | Component $schema = [];

    public static function make(HasForms $livewire): static
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
        $schema = $this->schema;

        if (is_array($this->schema)) {
            $schema = Grid::make()
                ->schema($schema)
                ->columns($this->getColumns());
        }

        return [$schema];
    }
}
