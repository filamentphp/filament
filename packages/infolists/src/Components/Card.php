<?php

namespace Filament\Infolists\Components;

class Card extends Component
{
    use Concerns\EntanglesStateWithSingularRelationship;

    /**
     * @var view-string
     */
    protected string $view = 'filament-infolists::components.card';

    /**
     * @param  array<Component>  $schema
     */
    final public function __construct(array $schema = [])
    {
        $this->schema($schema);
    }

    /**
     * @param  array<Component>  $schema
     */
    public static function make(array $schema = []): static
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
