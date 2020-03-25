<?php

namespace Filament\Http\Components;

use Illuminate\View\Component;

class Well extends Component
{
    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return <<<'blade'
            <div {{ $attributes->merge(['class' => 'border border-gray-200 bg-gray-50 overflow-hidden rounded']) }}>
                <div class="p-5">
                    {{ $slot }}
                </div>      
            </div>
        blade;
    }
}