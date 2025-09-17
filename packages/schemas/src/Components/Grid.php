<?php

namespace Filament\Schemas\Components;

use Filament\Schemas\Components\Concerns\EntanglesStateWithSingularRelationship;
use Filament\Schemas\Components\Contracts\CanEntangleWithSingularRelationships;

class Grid extends Component implements CanEntangleWithSingularRelationships
{
    use EntanglesStateWithSingularRelationship;

    /**
     * @var view-string
     */
    protected string $view = 'filament-schemas::components.grid';

    /**
     * @param  array<string, ?int> | int | null  $columns
     */
    final public function __construct(array | int | null $columns)
    {
        $this->columns($columns);
    }

    /**
     * @param  array<string, ?int> | int | null  $columns
     */
    public static function make(array | int | null $columns = 2): static
    {
        $static = app(static::class, ['columns' => $columns]);
        $static->configure();

        return $static;
    }
}
