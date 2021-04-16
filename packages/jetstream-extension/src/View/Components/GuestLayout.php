<?php

namespace Filament\JetstreamExtension\View\Components;

use Illuminate\View\Component;

class GuestLayout extends Component
{
    /**
     * Get the view / contents that represents the component.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function render()
    {
        return view('layouts.guest');
    }
}
