<?php

namespace Filament\Panel\Concerns;

trait HasFavicon
{
    protected ?string $favicon = null;

    public function favicon(?string $url): static
    {
        $this->favicon = $url;

        return $this;
    }

    public function getFavicon(): ?string
    {
        return $this->favicon;
    }
}
