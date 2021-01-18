<?php

namespace Filament\Http\Livewire;

use Livewire\Component;

class Dashboard extends Component
{
    public function render(): \Illuminate\View\View
    {
        return view('filament::livewire.dashboard',['placeholder' => __('filament::dashboard.placeholder', ['name' => config('app.name')])])
                    ->layout('filament::layouts.app', ['title' => __('filament::dashboard.title')]);
    }
}