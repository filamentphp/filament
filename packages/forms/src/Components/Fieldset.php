<?php

namespace Filament\Forms\Components;

use Closure;
use Filament\Forms\Components\Contracts\CanEntangleWithSingularRelationships;
use Illuminate\Contracts\Support\Htmlable;

class Fieldset extends Component implements CanEntangleWithSingularRelationships
{
    use Concerns\CanBeMarkedAsRequired;
    use Concerns\EntanglesStateWithSingularRelationship;

    /**
     * @var view-string
     */
    protected string $view = 'filament-forms::components.fieldset';

    final public function __construct(string | Htmlable | Closure | null $label = null)
    {
        $this->label($label);
    }

    public static function make(string | Htmlable | Closure | null $label = null): static
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

    public function isRequired(): bool
    {
        return false;
    }
}
