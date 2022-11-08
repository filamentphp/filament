<?php

namespace Filament\Forms\Components\Concerns;

use Filament\Forms\ComponentContainer;
use Filament\Forms\Contracts\HasForms;

trait BelongsToContainer
{
    protected ComponentContainer $container;

    public function container(ComponentContainer $container): static
    {
        $this->container = $container;

        return $this;
    }

    public function getContainer(): ComponentContainer
    {
        return $this->container;
    }

    public function getLivewire(): HasForms
    {
        return $this->getContainer()->getLivewire();
    }

    public function getLivewireKey(): string
    {
        $key = $this->getStatePath() . '.' . static::class;

        if ($containerKey = $this->getContainer()->getLivewireKey()) {
            $key = "{$containerKey}.{$key}";
        }

        return $key;
    }
}
