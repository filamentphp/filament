<?php

namespace Filament\Schema\Components;

use Closure;
use Filament\Schema\Components\Concerns\EntanglesStateWithSingularRelationship;
use Filament\Schema\Components\Contracts\CanEntangleWithSingularRelationships;
use Illuminate\Contracts\Support\Htmlable;

class Fieldset extends Component implements CanEntangleWithSingularRelationships
{
    use EntanglesStateWithSingularRelationship;

    /**
     * @var view-string
     */
    protected string $view = 'filament-schema::components.fieldset';

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
}
