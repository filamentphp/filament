<?php

namespace Filament\Panel\Concerns;

use Filament\AvatarProviders\UiAvatarsProvider;

trait HasAvatars
{
    protected string $defaultAvatarProvider = UiAvatarsProvider::class;

    public function defaultAvatarProvider(string $provider): static
    {
        $this->defaultAvatarProvider = $provider;

        return $this;
    }

    public function getDefaultAvatarProvider(): string
    {
        return $this->defaultAvatarProvider;
    }
}
