<?php

namespace Filament\Http\Livewire;

use Filament\Page;
use Livewire\Component;

class Dashboard extends Page
{
    public function render()
    {
        return view('filament::.dashboard', [
            //...
        ])->layout('filament::components.layouts.app');
    }
}
