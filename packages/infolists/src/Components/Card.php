<?php

namespace Filament\Infolists\Components;

use Closure;

class Card extends Component
{
    use Concerns\EntanglesStateWithSingularRelationship;

    /**
     * @var view-string
     */
    protected string $view = 'filament-infolists::components.card';

    /**
     * @param  array<Component> | Closure  $schema
     */
    final public function __construct(array | Closure $schema = [])
    {
        $this->schema($schema);
    }

    /**
     * @param  array<Component> | Closure  $schema
     */
    public static function make(array | Closure $schema = []): static
    {
        $static = app(static::class, ['schema' => $schema]);
        $static->configure();

        return $static;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->columnSpan('full');
    }
}
