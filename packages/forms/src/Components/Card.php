<?php

namespace Filament\Forms\Components;

class Card extends Component
{
    protected string $view = 'forms::components.card';

    final public function __construct(array $schema = [])
    {
        $this->schema($schema);
    }

    public static function make(array $schema = []): static
    {
        $static = new static($schema);
        $static->setUp();

        return $static;
    }

    protected function setUp(): void
    {
        $this->columnSpan('full');
    }
}
