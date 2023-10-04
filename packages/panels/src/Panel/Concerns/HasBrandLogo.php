<?php

namespace Filament\Panel\Concerns;

use Closure;

trait HasBrandLogo
{
    protected string | Closure | null $brandLogo = null;

    public function brandLogo(string | Closure | null $url): static
    {
        $this->brandLogo = $url;

        return $this;
    }

    public function getBrandLogo(): ?string
    {
        return $this->evaluate($this->brandLogo);
    }
}
