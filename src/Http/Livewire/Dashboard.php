<?php

namespace Filament\Http\Livewire;

use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        return view('filament::.dashboard', [
            //...
        ])->layout('filament::components.layouts.app');
    }
}
