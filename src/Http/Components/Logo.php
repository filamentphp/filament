<?php

namespace Filament\Http\Components;

use Illuminate\View\Component;

class Logo extends Component
{
    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('filament::components.logo');
    }
}