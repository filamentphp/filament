<?php

namespace Filament\Context\Concerns;

trait HasFavicon
{
    protected ?string $favicon = null;

    public function favicon(?string $url): void
    {
        $this->favicon = $url;
    }

    public function getFavicon(): ?string
    {
        return $this->favicon;
    }
}
