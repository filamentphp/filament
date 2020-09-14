<?php

namespace Filament\View\Components;

use Illuminate\View\Component;
use Filament\Filament;

class DropdownUserManagement extends Component
{
    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|\Closure|string|null
     */
    public function render()
    {
        if (! Filament::hasUserManagementFeatures()) {
            return;
        }

        return view('filament::components.dropdown-user-management');
    }
}