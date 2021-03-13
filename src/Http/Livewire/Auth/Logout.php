<?php

namespace Filament\Http\Livewire\Auth;

use Filament\Filament;
use Livewire\Component;

class Logout extends Component
{
    public $class;

    public function render()
    {
        return view('filament::auth.logout');
    }

    public function submit()
    {
        Filament::auth()->logout();

        return redirect()->route('filament.auth.login');
    }
}
