<?php

namespace Filament\Panel\Concerns;

use Closure;
use Filament\AvatarProviders\UiAvatarsProvider;

trait HasAvatars
{
    protected string | Closure $defaultAvatarProvider = UiAvatarsProvider::class;

    public function defaultAvatarProvider(string | Closure $provider): static
    {
        $this->defaultAvatarProvider = $provider;

        return $this;
    }

    public function getDefaultAvatarProvider(): string
    {
        return $this->evaluate($this->defaultAvatarProvider);
    }
}
