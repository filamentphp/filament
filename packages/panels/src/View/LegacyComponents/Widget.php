<?php

namespace Filament\View\LegacyComponents;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Widget extends Component
{
    public function render(): View
    {
        return view('filament-widgets::components.widget');
    }
}
