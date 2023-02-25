<?php

namespace Filament\Context\Concerns;

use Exception;
use Filament\GlobalSearch\Contracts\GlobalSearchProvider;
use Filament\GlobalSearch\DefaultGlobalSearchProvider;

trait HasGlobalSearch
{
    protected string $globalSearchProvider = DefaultGlobalSearchProvider::class;

    public function globalSearchProvider(string $provider): static
    {
        if (! in_array(GlobalSearchProvider::class, class_implements($provider))) {
            throw new Exception("Global search provider {$provider} does not implement the " . GlobalSearchProvider::class . ' interface.');
        }

        $this->globalSearchProvider = $provider;

        return $this;
    }

    public function getGlobalSearchProvider(): GlobalSearchProvider
    {
        return app($this->globalSearchProvider);
    }
}
