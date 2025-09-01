<?php

namespace Filament\Schemas\Components\Concerns;

use Closure;
use Illuminate\Contracts\Support\Htmlable;

trait HasDescription
{
    protected string | Htmlable | Closure | null $description = null;

    protected bool $shouldTranslateDescription = false;

    public function description(string | Htmlable | Closure | null $description = null): static
    {
        $this->description = $description;

        return $this;
    }

    public function translateDescription(bool $shouldTranslateDescription = true): static
    {
        $this->shouldTranslateDescription = $shouldTranslateDescription;

        return $this;
    }

    public function getDescription(): string | Htmlable | null
    {
        $description = $this->evaluate($this->description);

        return (is_string($description) && $this->shouldTranslateDescription) ?
            __($description) :
            $description;
    }
}
