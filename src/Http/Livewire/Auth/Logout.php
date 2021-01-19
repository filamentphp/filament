<?php

namespace Filament\Http\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Logout extends Component
{
    public $class;

    public function logout()
    {
        Auth::guard('filament')->logout();

        return redirect()->route('filament.auth.login');
    }

    public function render()
    {
        return view('filament::livewire.auth.logout', ['label' => __('filament::auth.logout')]);
    }
}
