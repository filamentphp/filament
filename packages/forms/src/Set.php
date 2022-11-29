<?php

namespace Filament\Forms;

use Filament\Forms\Components\Component;

class Set
{
    public function __construct(
        protected Component $component,
    ) {
    }

    /**
     * @param  mixed  $state
     * @return mixed
     */
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
