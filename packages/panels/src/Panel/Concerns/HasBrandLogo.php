<?php

namespace Filament\Panel\Concerns;

trait HasBrandLogo
{
    protected ?string $brandLogo = null;

    public function brandLogo(?string $url): static
    {
        $this->brandLogo = $url;

        return $this;
    }

    public function getBrandLogo(): ?string
    {
        return $this->brandLogo;
    }
}
