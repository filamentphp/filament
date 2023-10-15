<?php

namespace Filament\Panel\Concerns;

use Closure;

trait HasFavicon
{
    protected string | Closure | null $favicon = null;

    public function favicon(string | Closure | null $url): static
    {
        $this->favicon = $url;

        return $this;
    }

    public function getFavicon(): ?string
    {
        return $this->evaluate($this->favicon);
    }
}
