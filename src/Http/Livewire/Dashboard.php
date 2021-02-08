<?php

namespace Filament\Http\Livewire;

use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        return view('filament::.dashboard', ['placeholder' => __('filament::dashboard.placeholder', ['name' => config('app.name')])])
            ->layout('filament::components.layouts.app');
    }
}
