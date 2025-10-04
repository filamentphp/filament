<?php

namespace Filament\Schemas\Components\Utilities;

use Filament\Schemas\Components\Component;

class Get
{
    public function __construct(
        protected Component $component,
    ) {}

    public function __invoke(string | Component $path = '', bool $isAbsolute = false): mixed
    {
        $livewire = $this->component->getLivewire();

        $path = $this->component->resolveRelativeStatePath($path, $isAbsolute);

        $component = ($this->component->getStatePath() === $path)
            ? $this->component
            : $this->component->getRootContainer()->getComponentByStatePath(
                $path,
                withHidden: true,
                withAbsoluteStatePath: true,
                skipComponentChildContainersWhileSearching: $this->component,
            );

        if (! $component) {
            return data_get($livewire, $path);
        }

        return $component->getState();
    }
}
