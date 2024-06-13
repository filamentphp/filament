<?php

namespace Filament\Schema\Components\Utilities;

use Exception;
use Filament\Schema\Components\Component;

class Get
{
    public function __construct(
        protected Component $component,
    ) {
    }

    public function __invoke(string | Component $key, bool $isAbsolute = false): mixed
    {
        $key = $this->component->resolveRelativeKey($key);

        $livewire = $this->component->getLivewire();

        $component = $livewire->getSchemaComponent($key);

        if (! $component) {
            throw new Exception("Component with key [{$key}] not found on Livewire component[" . $livewire::class . '].');
        }

        return $component->getState();
    }
}
