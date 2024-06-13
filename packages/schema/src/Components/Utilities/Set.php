<?php

namespace Filament\Schema\Components\Utilities;

use Exception;
use Filament\Schema\Components\Component;

class Set
{
    public function __construct(
        protected Component $component,
    ) {
    }

    public function __invoke(string | Component $key, mixed $state, bool $isAbsolute = false): mixed
    {
        $key = $this->component->resolveRelativeKey($key);

        $livewire = $this->component->getLivewire();

        $component = $livewire->getSchemaComponent($key);

        if (! $component) {
            throw new Exception("Component with key [{$key}] not found on Livewire component[" . $livewire::class . '].');
        }

        $state = $this->component->evaluate($state);

        $component->state($state);

        return $state;
    }
}
