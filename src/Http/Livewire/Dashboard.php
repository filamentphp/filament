<?php

namespace Filament\Http\Livewire;

use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        return view('filament::livewire.dashboard')
            ->layout('filament::layouts.app', ['title' => __('Dashboard')]);
    }
}