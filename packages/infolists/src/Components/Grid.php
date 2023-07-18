<?php

namespace Filament\Infolists\Components;

class Grid extends Component
{
    use Concerns\EntanglesStateWithSingularRelationship;

    /**
     * @var view-string
     */
    protected string $view = 'filament-infolists::components.grid';

    /**
     * @param  array<string, int | null> | int | null  $columns
     */
    final public function __construct(array | int | null $columns)
    {
        $this->columns($columns);
    }

    /**
     * @param  array<string, int | null> | int | null  $columns
     */
    public static function make(array | int | null $columns = 2): static
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
