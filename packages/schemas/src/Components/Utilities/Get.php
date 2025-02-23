<?php

namespace Filament\Schemas\Components\Utilities;

use Filament\Schemas\Components\Component;

class Get
{
    public function __construct(
        protected Component $component,
    ) {}

    public function __invoke(string | Component $key = '', bool $isAbsolute = false): mixed
    {
        $livewire = $this->component->getLivewire();

        $component = $livewire->getSchemaComponent(
            $this->component->resolveRelativeKey($key, $isAbsolute),
            withHidden: true,
            skipComponentChildContainersWhileSearching: $this->component,
        );

        if (! $component) {
            return data_get(
                $livewire,
                $this->component->resolveRelativeStatePath($key, $isAbsolute)
            );
        }

        return $component->getState();
    }
}
