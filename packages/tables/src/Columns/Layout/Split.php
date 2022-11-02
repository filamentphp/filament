<?php

namespace Filament\Tables\Columns\Layout;

use Closure;

class Split extends Component
{
    protected string $view = 'tables::columns.layout.split';

    protected string | Closure | null $fromBreakpoint = null;

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
