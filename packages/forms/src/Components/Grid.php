<?php

namespace Filament\Forms\Components;

use Filament\Forms\Components\Contracts\CanEntangleWithSingularRelationships;

class Grid extends Component implements CanEntangleWithSingularRelationships
{
    use Concerns\EntanglesStateWithSingularRelationship;

    /**
     * @var view-string
     */
    protected string $view = 'filament-forms::components.grid';

    /**
     * @param  array<string, int | string | null> | int | string | null  $columns
     */
    final public function __construct(array | int | string | null $columns)
    {
        $this->columns($columns);
    }

    /**
     * @param  array<string, int | string | null> | int | string | null  $columns
     */
    public static function make(array | int | string | null $columns = 2): static
    {
        $static = app(static::class, ['columns' => $columns]);
        $static->configure();

        return $static;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->columnSpan('full');
    }
}
