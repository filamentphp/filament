<?php

namespace Filament\Support\Concerns;

use Closure;
use Illuminate\Contracts\Support\Htmlable;

trait HasDescription
{
    protected string | Htmlable | Closure | null $description = null;

    public function description(string | Htmlable | Closure | null $description = null): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription(): string | Htmlable | null
    {
        return $this->evaluate($this->description);
    }
}
