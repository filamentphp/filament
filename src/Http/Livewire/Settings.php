<?php

namespace Filament\Http\Livewire;

use Livewire\Component;

class Settings extends Component
{
    public function render()
    {
        return view('filament::livewire.settings')
            ->layout('filament::layouts.app', ['title' => __('Settings')]);
    }
}