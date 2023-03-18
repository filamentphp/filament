<?php

namespace Filament\Tables\Table\Concerns;

use Closure;

trait HasQueryStringIdentifier
{
    protected string | Closure | null $queryStringIdentifier = null;

    public function queryStringIdentifier(string | Closure | null $identifier): static
    {
        $this->queryStringIdentifier = $identifier;

        return $this;
    }

    public function getQueryStringIdentifier(): ?string
    {
        return $this->evaluate($this->queryStringIdentifier);
    }
}
