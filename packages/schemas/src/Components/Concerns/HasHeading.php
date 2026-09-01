<?php

namespace Filament\Schemas\Components\Concerns;

use Closure;
use Illuminate\Contracts\Support\Htmlable;

trait HasHeading
{
    protected string | Htmlable | Closure | null $heading = null;

    public function heading(string | Htmlable | Closure | null $heading = null): static
    {
        $this->heading = $heading;

        return $this;
    }

    public function getHeading(): string | Htmlable | null
    {
        return $this->evaluate($this->heading);
    }
}
