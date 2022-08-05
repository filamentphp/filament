<?php

namespace Filament\Forms\Components;

use Filament\Forms\Components\Contracts\CanEntangleWithSingularRelationships;

class Fieldset extends Component implements CanEntangleWithSingularRelationships
{
    use Concerns\EntanglesStateWithSingularRelationship;

    protected string $view = 'forms::components.fieldset';

    final public function __construct(string $label)
    {
        $this->label($label);
    }

    public static function make(string $label): static
    {
        $static = app(static::class, ['label' => $label]);
        $static->configure();

        return $static;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->columnSpan('full');

        $this->columns(2);
    }
}
