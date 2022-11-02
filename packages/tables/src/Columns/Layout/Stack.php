<?php

namespace Filament\Tables\Columns\Layout;

use Closure;
use Filament\Tables\Columns\Concerns\HasAlignment;
use Filament\Tables\Columns\Concerns\HasSpace;

class Stack extends Component
{
    use HasAlignment;
    use HasSpace;

    protected string $view = 'tables::columns.layout.stack';

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
