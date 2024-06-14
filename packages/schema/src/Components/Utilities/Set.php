<?php

namespace Filament\Schema\Components\Utilities;

use Filament\Schema\Components\Component;

class Set
{
    public function __construct(
        protected Component $component,
    ) {
    }

    public function __invoke(string | Component $key, mixed $state, bool $isAbsolute = false): mixed
    {
        $livewire = $this->component->getLivewire();

        $component = $livewire->getSchemaComponent(
            $this->component->resolveRelativeKey($key),
        );

        $state = $this->component->evaluate($state);

        if ($component) {
            $component->state($state);
        } else {
            data_set(
                $livewire,
                $this->component->resolveRelativeStatePath($key, $isAbsolute),
                $state,
            );
        }

        return $state;
    }
}
