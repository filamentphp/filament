<?php

namespace Filament\Forms\Callbacks;

use Filament\Forms\Components\Component;

class Set extends Callback
{
    public function __invoke(string | Component $path, $state, bool $isAbsolute = false)
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
