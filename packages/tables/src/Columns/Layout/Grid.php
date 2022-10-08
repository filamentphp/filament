<?php

namespace Filament\Tables\Columns\Layout;

class Grid extends Component
{
    protected string $view = 'tables::columns.layout.grid';

    protected ?array $columns = null;

    final public function __construct(array | int | null $columns)
    {
        $this->columns($columns);
    }

    public static function make(array | int | null $columns = 2): static
    {
        $static = app(static::class, ['columns' => $columns]);
        $static->configure();

        return $static;
    }

    public function columns(array | int | null $columns = 2): static
    {
        if (! is_array($columns)) {
            $columns = [
                'lg' => $columns,
            ];
        }

        $this->columns = array_merge($this->columns ?? [], $columns);

        return $this;
    }

    public function getGridColumns(): ?array
    {
        return $this->columns;
    }
}
