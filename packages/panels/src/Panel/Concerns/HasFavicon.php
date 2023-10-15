<?php

namespace Filament\Panel\Concerns;

use Closure;
use Illuminate\Contracts\Support\Htmlable;

trait HasFavicon
{
    protected string | Htmlable | Closure | null $favicon = null;

    public function favicon(string | Htmlable | Closure | null $url): static
    {
        $this->favicon = $url;

        return $this;
    }

    public function getFavicon(): string | Htmlable | null
    {
        return $this->evaluate($this->favicon);
    }
}
