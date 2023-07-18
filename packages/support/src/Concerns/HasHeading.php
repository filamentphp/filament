<?php

namespace Filament\Support\Concerns;

use Closure;
use Illuminate\Contracts\Support\Htmlable;

trait HasHeading
{
    protected string | Htmlable | Closure $heading;

    public function heading(string | Htmlable | Closure $heading): static
    {
        $this->heading = $heading;

        return $this;
    }

    public function getHeading(): string | Htmlable
    {
        return $this->evaluate($this->heading);
    }
}
