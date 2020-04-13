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
            <div {{ $attributes->merge(['class' => 'border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-transparent overflow-hidden']) }}>
                <div class="p-5">
                    {{ $slot }}
                </div>      
            </div>
        blade;
    }
}