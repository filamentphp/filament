<?php

namespace Filament\Tables\Columns\Concerns;

use Closure;

trait HasAffixes
{
    protected string | Closure | null $prefix = null;

    protected string | Closure | null $suffix = null;

    public function prefix(string | Closure $prefix): static
    {
        $this->prefix = $prefix;

        return $this;
    }

    public function suffix(string | Closure $suffix): static
    {
        $this->suffix = $suffix;

        return $this;
    }

    public function getPrefix(): ?string
    {
        return $this->evaluate($this->prefix);
    }

    public function getSuffix(): ?string
    {
        return $this->evaluate($this->suffix);
    }
}
