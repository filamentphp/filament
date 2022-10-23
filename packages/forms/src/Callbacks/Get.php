<?php

namespace Filament\Forms\Callbacks;

use Filament\Forms\Components\Component;

class Get extends Callback
{
    public function __invoke(string | Component $path, bool $isAbsolute = false)
    {
        $livewire = $this->component->getLivewire();

        return data_get(
            $livewire,
            $this->component->generateRelativeStatePath($path, $isAbsolute)
        );
    }
}
