<?php

namespace Filament\Forms\Components;

class Group extends Component
{
    protected string $view = 'forms::components.group';

    final public function __construct(array $schema = [])
    {
        $this->schema($schema);
    }

    public static function make(array $schema = []): static
    {
        $static = app(static::class, ['schema' => $schema]);
        $static->setUp();

        return $static;
    }
}
