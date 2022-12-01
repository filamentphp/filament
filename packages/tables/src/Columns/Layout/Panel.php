<?php

namespace Filament\Tables\Columns\Layout;

use Closure;
use Filament\Tables\Columns\Column;

class Panel extends Component
{
    /**
     * @var view-string
     */
    protected string $view = 'filament-tables::columns.layout.panel';

    /**
     * @param  array<Column | Component> | Closure  $schema
     */
    final public function __construct(array | Closure $schema)
    {
        $this->schema($schema);
    }

    /**
     * @param  array<Column | Component> | Closure  $schema
     */
    public static function make(array | Closure $schema): static
    {
        $static = app(static::class, ['schema' => $schema]);
        $static->configure();

        return $static;
    }
}
