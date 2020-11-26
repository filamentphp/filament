<?php

namespace Filament\Http\Livewire;

use Livewire\Component;

class Profile extends Component
{
    public function render()
    {
        return view('filament::livewire.profile')
            ->layout('filament::layouts.app', ['title' => __('Profile')]);
    }
}