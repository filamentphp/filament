<?php

namespace App\Livewire;

use Livewire\Component;

class ComponentsDemo extends Component
{
    public string $component = 'button';

    public function mount(string $component = 'button'): void
    {
        $this->component = $component;
    }

    public function render()
    {
        return view('livewire.components-demo', [
            'component' => $this->component,
        ]);
    }
}
