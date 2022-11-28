<?php

namespace Filament\Forms\Components;

use Filament\Forms\Components\Contracts\CanEntangleWithSingularRelationships;

class Card extends Component implements CanEntangleWithSingularRelationships
{
    use Concerns\EntanglesStateWithSingularRelationship;

    /**
     * @var view-string
     */
    protected string $view = 'filament-forms::components.card';

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
