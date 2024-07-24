<?php

namespace Filament\Schema\Components\Utilities;

use Filament\Schema\Components\Component;

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
