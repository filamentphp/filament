<?php

namespace Filament\Forms;

use Filament\Forms\Components\Component;

class Get
{
    public function __construct(
        protected Component $component,
    ) {}

    public function __invoke(string | Component $path = '', bool $isAbsolute = false): mixed
    {
        $livewire = $this->component->getLivewire();

        return data_get(
            $livewire,
            $this->component->generateRelativeStatePath($path, $isAbsolute)
        );
    }
}
