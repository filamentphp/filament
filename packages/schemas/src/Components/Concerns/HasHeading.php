<?php

namespace Filament\Schemas\Components\Concerns;

use Closure;
use Illuminate\Contracts\Support\Htmlable;

trait HasHeading
{
    protected string | Htmlable | Closure | null $heading = null;

    protected bool $shouldTranslateHeading = false;

    public function heading(string | Htmlable | Closure | null $heading = null): static
    {
        $this->heading = $heading;

        return $this;
    }

    public function translateHeading(bool $shouldTranslateHeading = true): static
    {
        $this->shouldTranslateHeading = $shouldTranslateHeading;

        return $this;
    }

    public function getHeading(): string | Htmlable | null
    {
        $heading = $this->evaluate($this->heading);

        return (is_string($heading) && $this->shouldTranslateHeading) ?
            __($heading) :
            $heading;
    }
}
