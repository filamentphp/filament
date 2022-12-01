<?php

namespace Filament\Tables\Columns\Layout;

use Closure;
use Filament\Tables\Columns\Column;

class Split extends Component
{
    /**
     * @var view-string
     */
    protected string $view = 'filament-tables::columns.layout.split';

    protected string | Closure | null $fromBreakpoint = null;

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

    public function from(string | Closure | null $breakpoint): static
    {
        $this->fromBreakpoint = $breakpoint;

        return $this;
    }

    public function getFromBreakpoint(): ?string
    {
        return $this->evaluate($this->fromBreakpoint);
    }
}
