<?php

namespace Filament\Tables\Columns\Layout;

use Closure;

class Panel extends Component
{
    protected string $view = 'tables::columns.layout.panel';

    final public function __construct(array | Closure $schema)
    {
        $this->schema($schema);
    }

    public static function make(array | Closure $schema): static
    {
        $static = app(static::class, ['schema' => $schema]);
        $static->configure();

        return $static;
    }
}
