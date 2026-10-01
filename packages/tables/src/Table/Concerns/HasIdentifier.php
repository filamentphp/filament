<?php

namespace Filament\Tables\Table\Concerns;

use Closure;

trait HasIdentifier
{
    protected string | Closure | null $identifier = null;

    public function identifier(string | Closure | null $identifier): static
    {
        $this->identifier = $identifier;

        return $this;
    }

    public function getIdentifier(): ?string
    {
        return $this->evaluate($this->identifier);
    }
}
