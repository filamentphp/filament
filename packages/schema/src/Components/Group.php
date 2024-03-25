<?php

namespace Filament\Schema\Components;

use Closure;
use Filament\Schema\Components\Contracts\CanEntangleWithSingularRelationships;

class Group extends Component implements CanEntangleWithSingularRelationships
{
    use \Filament\Schema\Components\Concerns\EntanglesStateWithSingularRelationship;

    /**
     * @var view-string
     */
    protected string $view = 'filament-schema::components.group';

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
}
