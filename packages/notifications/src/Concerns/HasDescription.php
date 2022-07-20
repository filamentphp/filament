<?php

namespace Filament\Notifications\Concerns;

use Closure;

trait HasDescription
{
    protected string | Closure | null $description = null;

    public function description(string | Closure | null $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->evaluate($this->description);
    }
}
