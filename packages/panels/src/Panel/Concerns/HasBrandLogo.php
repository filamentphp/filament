<?php

namespace Filament\Panel\Concerns;

use Closure;

trait HasBrandLogo
{
    protected string | Closure | null $brandLogo = null;

    public function brandLogo(string | Closure | null $path): static
    {
        $this->brandLogo = $path;

        return $this;
    }

    public function getBrandLogo(): ?string
    {
        return $this->evaluate($this->brandLogo);
    }
}
