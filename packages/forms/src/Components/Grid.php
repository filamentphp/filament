<?php

namespace Filament\Forms\Components;

class Grid extends Component
{
    protected string $view = 'forms::components.grid';

    final public function __construct(array | int | null $columns)
    {
        $this->columns($columns);
    }

    public static function make(array | int | null $columns = 2): static
    {
        $static = new static($columns);
        $static->setUp();

        return $static;
    }

    protected function setUp(): void
    {
        $this->columnSpan('full');
    }
}
