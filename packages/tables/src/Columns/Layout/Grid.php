<?php

namespace Filament\Tables\Columns\Layout;

class Grid extends Component
{
    /**
     * @var view-string
     */
    protected string $view = 'filament-tables::columns.layout.grid';

    /**
     * @var array<string, int | null> | null
     */
    protected ?array $columns = null;

    /**
     * @param  array<string, int | string | null> | int | string | null  $columns
     */
    final public function __construct(array | int | string | null $columns = 2)
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

    /**
     * @param  array<string, int | string | null> | int | string | null  $columns
     */
    public function columns(array | int | string | null $columns = 2): static
    {
        if (! is_array($columns)) {
            $columns = [
                'lg' => $columns,
            ];
        }

        $this->columns = [
            ...($this->columns ?? []),
            ...$columns,
        ];

        return $this;
    }

    /**
     * @return array<string, int | null> | null
     */
    public function getGridColumns(): ?array
    {
        return $this->columns;
    }
}
