<?php

namespace Filament\Panel\Concerns;

use Closure;
use Illuminate\Contracts\Support\Htmlable;

trait HasBrandLogo
{
    protected string | Htmlable | Closure | null $brandLogo = null;

    protected string | Closure | null $brandLogoHeight = null;

    protected string | Htmlable | Closure | null $darkModeBrandLogo = null;

    public function brandLogo(string | Htmlable | Closure | null $logo): static
    {
        $this->brandLogo = $logo;

        return $this;
    }

    public function brandLogoHeight(string | Closure | null $height): static
    {
        $this->brandLogoHeight = $height;

        return $this;
    }

    public function darkModeBrandLogo(string | Htmlable | Closure | null $logo): static
    {
        $this->darkModeBrandLogo = $logo;

        return $this;
    }

    public function getBrandLogo(): string | Htmlable | null
    {
        return $this->evaluate($this->brandLogo);
    }

    public function getBrandLogoHeight(): ?string
    {
        return $this->evaluate($this->brandLogoHeight);
    }

    public function getDarkModeBrandLogo(): string | Htmlable | null
    {
        return $this->evaluate($this->darkModeBrandLogo);
    }
}
