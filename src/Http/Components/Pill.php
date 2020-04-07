<?php

namespace Filament\Http\Components;

use Illuminate\View\Component;

class Pill extends Component
{
    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return <<<'blade'
            <span {{ $attributes->merge(['class' => 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium leading-4 text-gray-800 dark:text-gray-50 bg-gray-100 dark:bg-gray-500']) }}>
                {{ $slot }}     
            </span>
        blade;
    }
}
