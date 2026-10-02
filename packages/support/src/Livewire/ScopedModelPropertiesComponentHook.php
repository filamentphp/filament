<?php

namespace Filament\Support\Livewire;

use Filament\Support\Livewire\Contracts\HasScopedModelProperties;
use Livewire\ComponentHook;
use Livewire\Features\SupportLazyLoading\SupportLazyLoading;

class ScopedModelPropertiesComponentHook extends ComponentHook
{
    protected bool $isPendingLazyLoad = false;

    public function skip(): bool
    {
        return ! ($this->component instanceof HasScopedModelProperties);
    }

    /** @param array<string, mixed> $memo */
    public function hydrate(array $memo): void
    {
        $this->component->resolveScopedModelProperties($this->component->all());

        $this->isPendingLazyLoad = ($memo['lazyLoaded'] ?? null) === false;
    }

    /** @param array<mixed> $parameters */
    public function call(string $method, array $parameters): void
    {
        abort_if(in_array($method, [
            'resolveScopedModelProperties',
            'setParentRecordFromPageTableWidget',
        ]), 404);

        if ($this->isPendingLazyLoad && ($method === '__lazyLoad')) {
            $lazyLoadingHook = new SupportLazyLoading;
            $lazyLoadingHook->setComponent($this->component);

            $this->component->resolveScopedModelProperties(
                $lazyLoadingHook->resurrectMountParams($parameters[0]),
            );

            return;
        }

        $this->component->resolveScopedModelProperties();
    }
}
