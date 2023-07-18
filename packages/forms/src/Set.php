<?php

namespace Filament\Forms;

use Filament\Forms\Components\Component;

class Set
{
    public function __construct(
        protected Component $component,
    ) {
    }

    public function __invoke(string | Component $path, mixed $state, bool $isAbsolute = false): mixed
    {
        $livewire = $this->component->getLivewire();

        data_set(
            $livewire,
            $this->component->generateRelativeStatePath($path, $isAbsolute),
            $this->component->evaluate($state),
        );

        return $state;
    }
}
