<?php

namespace Filament\Support\Grid;

use Closure;
use Filament\Support\Concerns\EvaluatesClosures;

class GridManager
{
    use EvaluatesClosures;

    protected string | Closure | null $defaultBreakpoint = null;

    public function defaultBreakpoint(string | Closure | null $breakpoint = null): void
    {
        $this->defaultBreakpoint = $breakpoint;
    }

    public function getDefaultBreakpoint(): string
    {
        return $this->evaluate($this->defaultBreakpoint) ?? config('filament.default_grid_breakpoint', 'lg');
    }
}
