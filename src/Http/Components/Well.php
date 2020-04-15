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
            <div {{ $attributes->merge(['class' => 'rounded border border-gray-100 dark:border-gray-700 bg-gray-100 dark:bg-transparent overflow-hidden']) }}>
                <div class="p-5">
                    {{ $slot }}
                </div>      
            </div>
        blade;
    }
}