<?php

namespace Filament\Forms\Components;

class Livewire extends Component
{
    /**
     * @param string $component
     * @param array|null $componentData
     */
    final public function __construct(string $component, ?array $componentData = [])
    {
        $this->view('filament-forms::components.livewire', ['component' => $component, 'componentData' => $componentData]);
    }

    /**
     * @param string $component
     * @param array|null $componentData
     * @return Livewire
     */
    public static function make(string $component, ?array $componentData = []): static
    {
        return app(static::class, ['component' => $component, 'componentData' => $componentData]);
    }
}
